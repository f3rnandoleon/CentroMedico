from PIL import Image
from flask import Flask, request, jsonify
import torch
import torch.nn as nn
import torchvision.transforms as transforms
from io import BytesIO
import numpy as np
from sklearn.metrics.pairwise import cosine_similarity

# Detectar si hay GPU disponible
device = torch.device("cuda" if torch.cuda.is_available() else "cpu")

# Funciones auxiliares para extraer y comparar características
def extract_features(activations):
    """
    Extrae un resumen (por ejemplo, el promedio) de cada mapa de activación.
    Se asume que activations es un diccionario con tensores de activación para cada capa.
    """
    features = {}
    for key, value in activations.items():
        # Promediar sobre las dimensiones espaciales (H, W) y eliminar la dimensión de batch
        features[key] = value.mean(dim=[2, 3]).squeeze(0).cpu().numpy()
    return features

def compare_features(features1, features2):
    """
    Compara las características de dos imágenes (en forma de diccionario) usando similitud coseno.
    Devuelve un diccionario con la similitud (en porcentaje) para cada capa.
    """
    similarity_scores = {}
    for key in features1.keys():
        f1 = features1[key]
        f2 = features2[key]
        # Calcular la similitud coseno (añadimos un pequeño valor para evitar división por cero)
        cos_sim = np.dot(f1, f2) / (np.linalg.norm(f1) * np.linalg.norm(f2) + 1e-8)
        similarity_scores[key] = cos_sim * 100  # Convertir a porcentaje
    return similarity_scores

# Definir la arquitectura del modelo (debe coincidir con la usada en el entrenamiento)
class CNN_Model(nn.Module):
    def __init__(self):
        super(CNN_Model, self).__init__()
        self.conv_layers = nn.Sequential(
            nn.Conv2d(in_channels=3, out_channels=32, kernel_size=3, padding=1),
            nn.ReLU(),
            nn.BatchNorm2d(32),
            nn.Conv2d(in_channels=32, out_channels=32, kernel_size=3, padding=1),
            nn.ReLU(),
            nn.BatchNorm2d(32),
            nn.MaxPool2d(2),
            nn.Conv2d(in_channels=32, out_channels=64, kernel_size=3, padding=1),
            nn.ReLU(),
            nn.BatchNorm2d(64),
            nn.Conv2d(in_channels=64, out_channels=64, kernel_size=3, padding=1),
            nn.ReLU(),
            nn.BatchNorm2d(64),
            nn.MaxPool2d(2),
            nn.Conv2d(in_channels=64, out_channels=128, kernel_size=3, padding=1),
            nn.ReLU(),
            nn.BatchNorm2d(128),
            nn.Conv2d(in_channels=128, out_channels=128, kernel_size=3, padding=1),
            nn.ReLU(),
            nn.BatchNorm2d(128),
            nn.MaxPool2d(2),
            nn.Conv2d(in_channels=128, out_channels=256, kernel_size=3, padding=1),
            nn.ReLU(),
            nn.BatchNorm2d(256),
            nn.Conv2d(in_channels=256, out_channels=256, kernel_size=3, padding=1),
            nn.ReLU(),
            nn.BatchNorm2d(256),
            nn.MaxPool2d(2)
        )
        self.dense_layers = nn.Sequential(
            nn.Flatten(),
            nn.Dropout(0.4),
            nn.Linear(50176, 1024),
            nn.ReLU(),
            nn.Dropout(0.4),
            nn.Linear(1024, 2)
        )
        # Diccionario para almacenar las activaciones de las capas convolucionales
        self.activations = {}

    def forward(self, X, return_embedding=False):
        # Reiniciar el diccionario de activaciones
        self.activations = {}
        out = X
        for i, layer in enumerate(self.conv_layers):
            out = layer(out)
            if isinstance(layer, nn.Conv2d):
                self.activations[f'conv_{i+1}'] = out.detach().cpu()
        embedding = self.dense_layers[:-2](out)  # Extraer embeddings (antes de la última capa)
        output = self.dense_layers[-2:](embedding)  # Últimas dos capas para clasificación
        if return_embedding:
            return output, embedding
        else:
            return output

# Cargar el modelo y sus pesos (asegúrate de que la ruta y el nombre sean correctos)
model = CNN_Model().to(device)
model.load_state_dict(torch.load("best50_model_0.1547.pth", map_location=device))
model.eval()  # Modo evaluación

# Definir transformaciones de imagen
image_transforms = transforms.Compose([
    transforms.Resize((224, 224)),
    transforms.ToTensor(),
    transforms.Normalize([0.5, 0.5, 0.5], [0.5, 0.5, 0.5])
])

# Cargar embeddings, etiquetas y rutas del conjunto de entrenamiento
train_embeddings = torch.load("train_embeddings.pth")
train_labels = torch.load("train_labels.pth")
train_image_paths = torch.load("train_image_paths.pth")

# Función para encontrar imágenes similares (ya definida previamente)
def find_similar_images(query_embedding, train_embeddings, train_labels, top_k=5):
    query_embedding = query_embedding.squeeze(0)
    train_embeddings = train_embeddings.squeeze()
    similarities = cosine_similarity(query_embedding.unsqueeze(0), train_embeddings)
    top_k_indices = similarities.argsort()[0][-top_k:][::-1]
    top_k_indices = top_k_indices.copy()
    top_k_indices = torch.tensor(top_k_indices, dtype=torch.long)
    return top_k_indices, train_labels[top_k_indices]

# Iniciar Flask
app = Flask(__name__)

@app.route('/predict', methods=['POST'])
def predict():
    try:
        if 'file' not in request.files:
            return jsonify({'error': 'No se ha subido ninguna imagen'}), 400

        # Leer y preprocesar la imagen de entrada
        file = request.files['file']
        img = Image.open(BytesIO(file.read())).convert("RGB")
        img_tensor = image_transforms(img).unsqueeze(0).to(device)

            # Predicción y extracción del embedding
        # Realizar la predicción y obtener el embedding
        with torch.no_grad():
            outputs, query_embedding = model(img_tensor, return_embedding=True)
            probabilities = torch.softmax(outputs, dim=1)[0]
            predicted_class = torch.argmax(probabilities).item()
            probability_percentage = round(probabilities[predicted_class].item() * 100, 2)

        # Filtrar todos los embeddings de entrenamiento que sean Melanoma (label 0)
        melanoma_indices = [i for i, lbl in enumerate(train_labels) if int(lbl.item()) == 0]
        if len(melanoma_indices) == 0:
            # Si no hay imágenes melanoma en el conjunto, se retorna sin comparación de características
            filtered_similar_image_paths = []
            filtered_labels = []
            semantic_matches = {}
        else:
            # Extraer los embeddings correspondientes (asumiendo que train_embeddings es un tensor)
            melanoma_embeddings = train_embeddings[melanoma_indices]
            # Aplanar ambos embeddings a 2D
            query_flat = query_embedding.view(query_embedding.size(0), -1)
            melanoma_embeddings_flat = melanoma_embeddings.view(melanoma_embeddings.size(0), -1)

            # Calcular similitud coseno entre el embedding de la imagen consultada y todos los embeddings melanoma
            similarities = cosine_similarity(query_flat.cpu().numpy(), melanoma_embeddings_flat.cpu().numpy())

            # Número de imágenes similares a retornar
            top_k = 5
            # Obtener los índices (dentro del subconjunto de melanoma) de los top k más similares
            topk_within = similarities.argsort()[0][-top_k:][::-1]
            # Obtener los índices reales en el conjunto de entrenamiento
            filtered_indices = [melanoma_indices[i] for i in topk_within]
            filtered_labels = [0] * len(filtered_indices)
            filtered_similar_image_paths = [train_image_paths[i] for i in filtered_indices]

            # Extraer características de la imagen consultada (utilizando las activaciones del último forward)
            test_features = extract_features(model.activations)

            # Para cada imagen similar filtrada, cargarla, pasarla por el modelo y extraer sus características
            similar_features_list = []
            for idx in filtered_indices:
                sim_img = Image.open(train_image_paths[idx]).convert("RGB")
                sim_img_tensor = image_transforms(sim_img).unsqueeze(0).to(device)
                with torch.no_grad():
                    _ = model(sim_img_tensor)
                sim_features = extract_features(model.activations)
                similar_features_list.append(sim_features)

            # Comparar características: se calcula la similitud en cada capa para cada imagen similar
            similarity_results = []
            for i, sim_features in enumerate(similar_features_list):
                similarity_scores = compare_features(test_features, sim_features)
                similarity_results.append((i + 1, similarity_scores))

            # Determinar las mejores coincidencias por capa
            best_matches = {}
            for key in test_features.keys():
                best_match = max(similarity_results, key=lambda x: x[1][key])
                best_matches[key] = {'match_index': best_match[0], 'score': float(best_match[1][key])}

            # Mapeo de claves de activaciones a etiquetas semánticas deseadas
            mapping = {
                'conv_1': 'color',
                'conv_4': 'borde',
                'conv_8': 'asimetria',
                'conv_11': 'textura'
            }
            semantic_matches = {}
            for key, value in best_matches.items():
                if key in mapping:
                    semantic_matches[mapping[key]] = value

        classes = ["Melanoma", "No Melanoma"]

        return jsonify({
            'prediction': classes[predicted_class],
            'probability': probability_percentage,
            'similar_images': filtered_similar_image_paths,
            'similar_labels': filtered_labels,
            'best_matches': semantic_matches
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
