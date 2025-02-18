# Cargar el modelo desde el archivo .h5
from PIL import Image
from flask import Flask, request, jsonify
import numpy as np
import tensorflow as tf
from tensorflow.keras.preprocessing import image
from tensorflow.keras.models import load_model
from io import BytesIO
# Cargar el modelo guardado en formato .h5
model = load_model("melanoma_detection_model.keras")

# Iniciar la aplicación Flask
app = Flask(__name__)

@app.route('/predict', methods=['POST'])
def predict():
    try:
        # Verificar si se subió una imagen
        if 'file' not in request.files:
            return jsonify({'error': 'No se ha subido ninguna imagen'}), 400

        file = request.files['file']
        # Leer el archivo en memoria
        img_bytes = file.read()
        
        # Convertir el archivo a un objeto BytesIO
        img = Image.open(BytesIO(img_bytes))
        
        # Redimensionar la imagen para que tenga el tamaño que espera el modelo
        img = img.resize((224, 224))

        # Convertir la imagen a un array y normalizar
        img_array = np.array(img) / 255.0
        img_array = np.expand_dims(img_array, axis=0)  # Agregar dimensión de batch
        

      # Hacer predicción
        prediction = model.predict(img_array)
        probability = float(prediction[0][0])  # Convertir a float
        result = "Melanoma" if probability > 0.5 else "No Melanoma"
        probability_percentage = round(probability * 100, 2)  # Convertir a porcentaje

        # Devolver la predicción y el porcentaje
        return jsonify({
            'prediction': result,
            'probability': probability_percentage
        })
    
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
