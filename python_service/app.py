import os
import requests
import io
from PIL import Image
from flask import Flask, request, jsonify
from flask_cors import CORS
from cnn_engine import CNNEngine
from chatbot_engine import BrahmmyChatbot

app = Flask(__name__)
CORS(app)

cnn_engine = CNNEngine()
chatbot = BrahmmyChatbot()

def load_image_from_path_or_url(source):
    """
    Helper to load image from local filepath or HTTP URL.
    """
    if source.startswith('http://') or source.startswith('https://'):
        resp = requests.get(source, timeout=3)
        return Image.open(io.BytesIO(resp.content))
    else:
        return Image.open(source)

@app.route('/', methods=['GET'])
@app.route('/health', methods=['GET'])
def health():
    return jsonify({
        "status": "online",
        "service": "Barako Track Python AI Microservice",
        "features": ["CNN Image Similarity Matching", "Brahmmy Chatbot Engine"]
    }), 200

@app.route('/extract-features', methods=['POST'])
def extract_features():
    """
    Accepts an uploaded image file and returns its normalized feature vector.
    """
    if 'image' not in request.files:
        return jsonify({"error": "No image file provided in request"}), 400

    file = request.files['image']
    if file.filename == '':
        return jsonify({"error": "Empty filename"}), 400

    try:
        image_bytes = file.read()
        vector = cnn_engine.extract_features(image_bytes)
        return jsonify({
            "status": "success",
            "feature_vector": vector,
            "dimensions": len(vector)
        }), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route('/compare-features', methods=['POST'])
def compare_features():
    """
    Compares two feature vectors and returns the visual similarity percentage (0-100%).
    """
    data = request.get_json() or {}
    vec1 = data.get('vec1')
    vec2 = data.get('vec2')

    if not vec1 or not vec2:
        return jsonify({"error": "Both vec1 and vec2 are required"}), 400

    score = cnn_engine.compute_similarity(vec1, vec2)
    return jsonify({
        "status": "success",
        "similarity_score": score,
        "is_potential_match": score > 45.0
    }), 200

@app.route('/compare-images', methods=['POST'])
def compare_images():
    """
    Directly compares two image filepaths/URLs using CNNEngine visual feature extraction.
    """
    data = request.get_json() or {}
    path1 = data.get('path1')
    path2 = data.get('path2')

    if not path1 or not path2:
        return jsonify({"error": "Both path1 and path2 image sources are required"}), 400

    try:
        img1 = load_image_from_path_or_url(path1)
        img2 = load_image_from_path_or_url(path2)

        vec1 = cnn_engine.extract_features(img1)
        vec2 = cnn_engine.extract_features(img2)

        score = cnn_engine.compute_similarity(vec1, vec2)
        return jsonify({
            "status": "success",
            "similarity_score": score,
            "is_potential_match": score > 45.0
        }), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route('/chatbot', methods=['POST'])
def chatbot_query():
    """
    Receives user query and returns Brahmmy Chatbot response.
    """
    data = request.get_json() or {}
    query = data.get('query', '')
    user_name = data.get('user_name', 'Student')

    if not query:
        return jsonify({"error": "Query parameter is missing"}), 400

    result = chatbot.get_response(query, user_name)
    return jsonify({
        "status": "success",
        "response": result
    }), 200

if __name__ == '__main__':
    port = int(os.environ.get('PORT', 5000))
    print(f"Starting Barako Track AI Microservice on port {port}...")
    app.run(host='0.0.0.0', port=port, debug=True)
