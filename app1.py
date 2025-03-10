from PIL import Image
from flask import Flask, request, jsonify
import torch
import torch.nn as nn
import torchvision.transforms as transforms
from io import BytesIO

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

    def forward(self, X):
        out = self.conv_layers(X)
        out = self.dense_layers(out)
        return out

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

        # Hacer predicción
        with torch.no_grad():
            outputs = model(img)
            probabilities = torch.softmax(outputs, dim=1)[0]  # Convertir a probabilidades
            predicted_class = torch.argmax(probabilities).item()
            probability_percentage = round(probabilities[predicted_class].item() * 100, 2)

        classes = ["Melanoma", "No Melanoma"]
        return jsonify({
            'prediction': classes[predicted_class],
            'probability': probability_percentage
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
