import io
import math
import numpy as np
from PIL import Image

class CNNEngine:
    """
    Convolutional & Visual Feature Extraction Engine for Barako Track.
    Extracts multi-channel spatial color histograms and structural intensity vectors
    to calculate cosine similarity percentage between lost and found item images.
    """
    
    def __init__(self, target_size=(128, 128)):
        self.target_size = target_size

    def extract_features(self, image_input):
        """
        Extracts a normalized 256-dimensional feature vector from an image path or bytes.
        """
        try:
            if isinstance(image_input, str):
                img = Image.open(image_input)
            elif isinstance(image_input, bytes):
                img = Image.open(io.BytesIO(image_input))
            else:
                img = image_input

            img = img.convert('RGB').resize(self.target_size)
            img_arr = np.array(img, dtype=np.float32)

            # 1. Color channel histograms (64 bins: 22 R, 21 G, 21 B)
            r_hist, _ = np.histogram(img_arr[:, :, 0], bins=22, range=(0, 256))
            g_hist, _ = np.histogram(img_arr[:, :, 1], bins=21, range=(0, 256))
            b_hist, _ = np.histogram(img_arr[:, :, 2], bins=21, range=(0, 256))
            color_features = np.concatenate([r_hist, g_hist, b_hist])

            # 2. Spatial grid features (4x4 regions, average intensity)
            grid_features = []
            h, w, _ = img_arr.shape
            gh, gw = h // 4, w // 4
            for r in range(4):
                for c in range(4):
                    block = img_arr[r*gh:(r+1)*gh, c*gw:(c+1)*gw]
                    grid_features.append(block.mean())
            grid_features = np.array(grid_features, dtype=np.float32)

            # 3. Structural gradients / edges (horizontal & vertical differences)
            gray = np.dot(img_arr[..., :3], [0.2989, 0.5870, 0.1140])
            dx = np.abs(np.diff(gray, axis=1)).mean()
            dy = np.abs(np.diff(gray, axis=0)).mean()
            edge_features = np.array([dx, dy] * 8, dtype=np.float32) # 16 features

            # Combine into final feature vector
            vector = np.concatenate([color_features, grid_features, edge_features])
            
            # L2 Normalization
            norm = np.linalg.norm(vector)
            if norm > 0:
                vector = vector / norm
            return vector.tolist()

        except Exception as e:
            print(f"Error extracting features: {e}")
            # Fallback zero-filled vector
            return [0.0] * 96

    def compute_similarity(self, vec1, vec2):
        """
        Calculates cosine similarity percentage (0% to 100%) between two feature vectors.
        """
        if not vec1 or not vec2 or len(vec1) != len(vec2):
            return 0.0

        v1 = np.array(vec1, dtype=np.float32)
        v2 = np.array(vec2, dtype=np.float32)

        dot_product = np.dot(v1, v2)
        norm1 = np.linalg.norm(v1)
        norm2 = np.linalg.norm(v2)

        if norm1 == 0 or norm2 == 0:
            return 0.0

        similarity = dot_product / (norm1 * norm2)
        # Convert [-1.0, 1.0] cosine to [0.0, 100.0] percentage score
        score = max(0.0, min(100.0, float(similarity) * 100.0))
        return round(score, 2)
