import io
import numpy as np
from PIL import Image

try:
    import torch
    import torchvision.models as models
    import torchvision.transforms as transforms
    HAS_TORCH = True
except ImportError:
    HAS_TORCH = False


class CNNEngine:
    """
    Convolutional Neural Network (CNN) Feature Extraction Engine for Barako Track.
    Uses pre-trained MobileNetV2 (ImageNet weights) to extract a 1280-dimensional 
    deep visual embedding vector and compute Cosine Similarity between images.
    """
    
    def __init__(self, target_size=(224, 224)):
        self.target_size = target_size
        self.model = None
        self.transform = None

        if HAS_TORCH:
            try:
                # Load pre-trained MobileNetV2 model
                weights = models.MobileNet_V2_Weights.DEFAULT
                self.model = models.mobilenet_v2(weights=weights)
                # Replace the final linear classification layer with Identity to extract 1280-dim feature vector
                self.model.classifier[1] = torch.nn.Identity()
                self.model.eval()

                # Define standard ImageNet preprocessing transforms
                self.transform = transforms.Compose([
                    transforms.Resize(self.target_size),
                    transforms.ToTensor(),
                    transforms.Normalize(
                        mean=[0.485, 0.456, 0.406],
                        std=[0.229, 0.224, 0.225]
                    )
                ])
                print("MobileNetV2 CNN Model initialized successfully.")
            except Exception as e:
                print(f"Error loading MobileNetV2 model: {e}")
                self.model = None

    def extract_features(self, image_input):
        """
        Extracts a normalized 1280-dimensional deep feature vector from an image path or bytes.
        """
        try:
            if isinstance(image_input, str):
                img = Image.open(image_input)
            elif isinstance(image_input, bytes):
                img = Image.open(io.BytesIO(image_input))
            else:
                img = image_input

            img = img.convert('RGB')

            if HAS_TORCH and self.model is not None and self.transform is not None:
                # Preprocess image tensor
                tensor = self.transform(img).unsqueeze(0)  # Add batch dimension [1, 3, 224, 224]

                with torch.no_grad():
                    # Forward pass through MobileNetV2 feature extractor
                    features = self.model(tensor) # Shape: [1, 1280]
                    vector = features.squeeze(0).cpu().numpy()

                # L2 Normalization
                norm = np.linalg.norm(vector)
                if norm > 0:
                    vector = vector / norm

                return vector.tolist()
            else:
                # Fallback custom feature extraction if torch is unavailable
                return self._fallback_extract_features(img)

        except Exception as e:
            print(f"Error extracting MobileNetV2 features: {e}")
            return [0.0] * 1280

    def _fallback_extract_features(self, img):
        """
        Fallback feature extraction algorithm if PyTorch is not yet installed.
        """
        img = img.resize((128, 128))
        img_arr = np.array(img, dtype=np.float32)

        r_hist, _ = np.histogram(img_arr[:, :, 0], bins=22, range=(0, 256))
        g_hist, _ = np.histogram(img_arr[:, :, 1], bins=21, range=(0, 256))
        b_hist, _ = np.histogram(img_arr[:, :, 2], bins=21, range=(0, 256))
        color_features = np.concatenate([r_hist, g_hist, b_hist])

        grid_features = []
        h, w, _ = img_arr.shape
        gh, gw = h // 4, w // 4
        for r in range(4):
            for c in range(4):
                block = img_arr[r*gh:(r+1)*gh, c*gw:(c+1)*gw]
                grid_features.append(block.mean())
        grid_features = np.array(grid_features, dtype=np.float32)

        gray = np.dot(img_arr[..., :3], [0.2989, 0.5870, 0.1140])
        dx = np.abs(np.diff(gray, axis=1)).mean()
        dy = np.abs(np.diff(gray, axis=0)).mean()
        edge_features = np.array([dx, dy] * 8, dtype=np.float32)

        vector = np.concatenate([color_features, grid_features, edge_features])
        norm = np.linalg.norm(vector)
        if norm > 0:
            vector = vector / norm

        # Pad vector to 1280 length for consistency
        padded = np.zeros(1280, dtype=np.float32)
        padded[:len(vector)] = vector
        return padded.tolist()

    def compute_similarity(self, vec1, vec2):
        """
        Calculates cosine similarity percentage (0% to 100%) between two 1280-dim feature vectors.
        """
        if not vec1 or not vec2:
            return 0.0

        v1 = np.array(vec1, dtype=np.float32)
        v2 = np.array(vec2, dtype=np.float32)

        # Truncate or pad if lengths differ
        min_len = min(len(v1), len(v2))
        v1 = v1[:min_len]
        v2 = v2[:min_len]

        dot_product = np.dot(v1, v2)
        norm1 = np.linalg.norm(v1)
        norm2 = np.linalg.norm(v2)

        if norm1 == 0 or norm2 == 0:
            return 0.0

        similarity = dot_product / (norm1 * norm2)
        # Convert [-1.0, 1.0] cosine similarity to [0.0, 100.0] percentage score
        score = max(0.0, min(100.0, float(similarity) * 100.0))
        return round(score, 2)