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
            nn.MaxPool2d(2),
        )

        self.dense_layers = nn.Sequential(
            nn.Flatten(),
            nn.Dropout(0.4),
            nn.Linear(50176, 1024),
            nn.ReLU(),
            nn.Dropout(0.4),
            nn.Linear(1024, 2)  # Dos clases: Melanoma y No Melanoma
        )

    def forward(self, X, return_embedding=False):
        out = self.conv_layers(X)
        embedding = self.dense_layers[:-2](out)  # Extraer embeddings (antes de la última capa)
        output = self.dense_layers[-2:](embedding)  # Últimas dos capas para clasificación
        
        if return_embedding:
            return output, embedding
        else:
            return output

# Cargar el modelo y enviarlo a la GPU si está disponible
model = CNN_Model().to(device)
model.load_state_dict(torch.load("best50_model_0.1547.pth", map_location=device))
model.eval()  # Modo evaluación

# Definir transformaciones de imagen
image_transforms = transforms.Compose([
    transforms.Resize((224, 224)),
    transforms.ToTensor(),
    transforms.Normalize([0.5, 0.5, 0.5], [0.5, 0.5, 0.5])
])

# Cargar embeddings y etiquetas del conjunto de entrenamiento
train_embeddings = torch.load("train_embeddings.pth")  # Asegúrate de guardar estos embeddings previamente
train_labels = torch.load("train_labels.pth")
train_image_paths = torch.load("train_image_paths.pth")
# Función para encontrar imágenes similares
def find_similar_images(query_embedding, train_embeddings, train_labels, top_k=5):
    # Asegurarse de que los embeddings sean 2D
    query_embedding = query_embedding.squeeze(0)  # Eliminar la dimensión del batch si existe
    train_embeddings = train_embeddings.squeeze()  # Eliminar dimensiones adicionales si existen
    
    # Calcular similitud coseno
    similarities = cosine_similarity(query_embedding.unsqueeze(0), train_embeddings)
    
    # Obtener los índices de las imágenes más similares
    top_k_indices = similarities.argsort()[0][-top_k:][::-1]
    
    # Hacer una copia explícita del array de NumPy para evitar strides negativos
    top_k_indices = top_k_indices.copy()
    
    # Convertir top_k_indices a un tensor de PyTorch
    top_k_indices = torch.tensor(top_k_indices, dtype=torch.long)
    
    # Devolver las imágenes y etiquetas más similares
    return top_k_indices, train_labels[top_k_indices]

# Iniciar Flask
app = Flask(__name__)

@app.route('/predict', methods=['POST'])
def predict():
    try:
        if 'file' not in request.files:
            return jsonify({'error': 'No se ha subido ninguna imagen'}), 400

        file = request.files['file']
        img = Image.open(BytesIO(file.read())).convert("RGB")
        img = image_transforms(img).unsqueeze(0).to(device)  # Agregar batch y mover a dispositivo

        # Hacer predicción y obtener embedding
        with torch.no_grad():
            outputs, query_embedding = model(img, return_embedding=True)
            probabilities = torch.softmax(outputs, dim=1)[0]  # Convertir a probabilidades
            predicted_class = torch.argmax(probabilities).item()
            probability_percentage = round(probabilities[predicted_class].item() * 100, 2)

        # Encontrar imágenes similares
        top_k_indices, similar_labels = find_similar_images(query_embedding, train_embeddings, train_labels, top_k=5)

        # Obtener las rutas completas de las imágenes similares
        similar_image_paths = [train_image_paths[i] for i in top_k_indices]

        classes = ["Melanoma", "No Melanoma"]
        return jsonify({
            'prediction': classes[predicted_class],
            'probability': probability_percentage,
            'similar_images': similar_image_paths,
            'similar_labels': similar_labels.tolist()
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)