import os
from flask import Flask, request, jsonify
from flask_cors import CORS
from cnn_engine import CNNEngine
from chatbot_engine import BrahmmyChatbot

app = Flask(__name__)
CORS(app)

cnn_engine = CNNEngine()
chatbot = BrahmmyChatbot()

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
    Accepts an uploaded image file and returns its normalized 96-dim feature vector.
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
        "is_potential_match": score >= 65.0
    }), 200

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
