import re

class BrahmmyChatbot:
    """
    Brahmmy Chatbot Engine for Barako Track.
    Handles campus lost-and-found student inquiries, step-by-step reporting guidance,
    office locations, FAQs, and intelligent intent parsing.
    """

    def __init__(self):
        self.bot_name = "Brahmmy"
        self.office_hours = "Monday to Friday: 8:00 AM - 5:00 PM | Saturday: 8:00 AM - 12:00 PM"
        self.office_location = "Student Affairs Office & Campus Security Headquarters (Ground Floor, Main Admin Building)"
        self.contact_email = "lostandfound@ub.edu.ph"
        self.contact_phone = "(043) 723-1425 ext. 104"

    def get_response(self, user_query, user_name="Student"):
        query = user_query.lower().strip()
        
        # 1. Greetings
        if any(w in query for w in ["hello", "hi", "hey", "good morning", "good afternoon", "greetings", "kumusta"]):
            return {
                "message": f"Hello {user_name}! I am Brahmmy, your official UB Barako Track Assistant. How can I help you recover or report a lost/found item today?",
                "suggestions": ["How to report a lost item?", "How to report a found item?", "Where is the Lost & Found office?", "Office Hours"]
            }

        # 2. How to Report / Surrender a Found Item
        if "found" in query or ("report" in query and "found" in query) or "turn in" in query or "surrender" in query:
            return {
                "message": "🏢 **How to Report a Found Item:**\nIf you found an item on campus, you can go to the **Student Affairs Office (SAO)** or Campus Security Headquarters (Ground Floor, Main Admin Building) to surrender the item so our SAO admin can register it into storage!",
                "suggestions": ["Where is the office?", "Office Hours", "How to report a lost item?"]
            }

        # 3. How to Report Lost Item
        if "report" in query and ("lost" in query or "missing" in query):
            return {
                "message": "📝 **Steps to Report a Lost Item:**\n1. Click **'Report Lost Item'** in the navigation menu.\n2. Enter the item details (Title, Category, Date Lost, Location Tag).\n3. Upload a clear photograph if available.\n4. Submit your report! Our CNN AI will automatically match your item against found listings.",
                "suggestions": ["Search Lost Items", "How to claim an item?", "Contact Support"]
            }

        # 4. Office Location & Hours
        if any(w in query for w in ["where", "location", "office", "headquarters", "building", "sao"]):
            return {
                "message": f"The Lost & Found Office is located at:\n📍 **{self.office_location}**\n\nContact: {self.contact_phone} | {self.contact_email}",
                "suggestions": ["Office Hours", "How to claim an item?", "Report Lost Item"]
            }

        if any(w in query for w in ["hour", "time", "open", "schedule", "close", "operating"]):
            return {
                "message": f"⏰ **Barako Track Office Hours:**\n{self.office_hours}\n\nOur team is available during these hours for physical item verifications and releases.",
                "suggestions": ["Where is the office?", "How to claim an item?", "Search Items"]
            }

        # 5. Claiming Process & Proof of Ownership
        if any(w in query for w in ["claim", "proof", "verify", "ownership", "get back"]):
            return {
                "message": "🛡️ **How to Claim Your Item:**\n1. Browse the **Found Items Directory**.\n2. If you see your item, click **'Submit Claim Request'**.\n3. Provide **Proof of Ownership** (e.g., specific marks, wallpaper description, serial numbers, receipt, or unique details).\n4. Wait for Campus Security/Admin review. Once approved, visit SAO with your Student ID to pick it up!",
                "suggestions": ["Where is the office?", "Office Hours", "Search Found Items"]
            }

        # 6. Categories Inquiry
        if any(w in query for w in ["category", "categories", "type", "what items"]):
            return {
                "message": "🏷️ **Supported Item Categories on Barako Track:**\n• Electronics (Phones, Laptops, Earbuds, Chargers)\n• IDs & Cards (Student IDs, ATM Cards, Driver's License)\n• Bags & Wallets (Backpacks, Purses, Wallets)\n• Books & Documents (Textbooks, Notebooks, Envelopes)\n• Keys & Accessories (Keys, Jewelry, Umbrellas, Eyeglasses)\n• Clothing & Uniforms",
                "suggestions": ["Search Items", "Report Lost Item"]
            }

        # 7. Contact / Help
        if any(w in query for w in ["contact", "email", "phone", "call", "help", "support", "security"]):
            return {
                "message": f"📞 **Campus Security & SAO Contact Info:**\n• Phone: {self.contact_phone}\n• Email: {self.contact_email}\n• Location: {self.office_location}",
                "suggestions": ["Office Hours", "How to claim an item?"]
            }

        # Default Response
        return {
            "message": f"I'm Brahmmy! I can assist you with campus lost-and-found queries. If you found an item on campus, you can go to the **Student Affairs Office (SAO)**!",
            "suggestions": ["How to report a found item?", "How to report a lost item?", "Where is the office?", "Office Hours"]
        }
