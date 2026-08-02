# GROUP PROJECT PROPOSAL
## Barako Track: A Smart Lost and Found Management System for School Campus Students

### Team Members
- **Decsten Matibag**
- **Russel Vincent Cuevas**
- **Mark Camilon**
- **Dave Kenneth**
- **Anna Sembrano**

---

### Project Information
- **Proposed Topic:** Barako Track: A Smart Lost and Found Management System for School Campus Students with Integrated Chatbot Support and CNN Image Matching
- **Target Users:** Students, Faculty, Staff, Administrators, and Campus Security Personnel
- **Campus Context:** Designed for a school campus where students need a simple, reliable, and transparent way to recover lost belongings.
- **Tech Stack:**
  - **Backend Framework:** Laravel (PHP 8.2+)
  - **AI / Smart Services:** Python (CNN Image Feature Extraction & Similarity Search + Brahmmy Chatbot Engine)
  - **Database:** MySQL / phpMyAdmin (`barako_track`)
  - **UI / Frontend Design:** Bootstrap 5 with Custom UB BarakoTrack CSS Design System

---

### Project Overview
Barako Track is a web-based Smart Lost and Found Management System intended for a school campus environment. The platform centralizes the process of reporting lost items, submitting found items, searching item records, requesting claims, and monitoring report status. Instead of relying only on manual logbooks, office visits, or informal announcements, Barako Track gives students and campus personnel a clear digital process for item recovery.

---

### Problem Statement
Campus lost-and-found management can become inefficient when reports are scattered across offices, messages, or paper records. Students may not know where to check, how to report an item, or how to confirm whether a found item belongs to the correct owner. This can delay recovery and create confusion. Barako Track addresses this by providing a guided, organized, and transparent system for both students and administrators.

---

### Objectives and Main Functions

#### Student-Focused Objectives
- Make lost-and-found services accessible online 24/7.
- Allow students to report lost or found items easily with photo uploads and location tags.
- Help students search items and track claim progress in real-time.
- Provide clear status updates and automated match recommendations.

#### Administrative & Security Objectives
- Support organized review of reports and claims with proof validation.
- Help verify ownership proof before releasing items.
- Reduce manual tracking, paper logbooks, and duplicate records.
- Improve transparency, accountability, and reporting metrics.

---

### Core System Features

1. **User Authentication & Authorization**: Role-based access control for Students, Administrators.
2. **Lost & Found Reporting**: Photo upload, date/time lost, location tag, detailed description, category assignment.
3. **Search & AI Visual Match (CNN)**: Convolutional Neural Network image similarity search matching lost items with found items.
4. **Claim Request & Proof Validation**: Structured claim submission requiring proof of ownership (identifying details, receipts, photos) and admin approval.
5. **Brahmmy Chatbot Support**:
   - Answers common lost-and-found FAQs.
   - Guides users through reporting and claiming steps.
   - Provides contact details and office hour information.
   - Supports keyword-based item search assistance.
6. **Notification System**: Instant alerts for claim updates, report status, and visual match detections.

---

### Ethical and Data Privacy Considerations
Since Barako Track handles student accounts, report details, uploaded item information, ownership proof, and claim records, the system is designed with responsible data handling. Access is limited to authorized users, sensitive item details (such as serial numbers or private proof) are protected, verification is fair, and users receive transparent updates about the status of their reports and claims.

---

### Design Palette (UB BarakoTrack Theme)

```css
:root {
    /* Brand Palette - UB BarakoTrack Colors */
    --primary-color: #752738;
    --primary-hover: #5a1e2c;
    --primary-light: #9a3a50;
    --secondary-color: #fec452;
    --secondary-hover: #ffd37a;
    --accent-color: #fec452;

    /* Backgrounds */
    --body-bg: #f4f6fb;
    --card-bg: #ffffff;
    --sidebar-bg: #1e1e2d;
    --sidebar-hover: rgba(254, 196, 82, 0.08);
    --sidebar-active: rgba(254, 196, 82, 0.14);

    /* Text Colors */
    --text-main: #1e293b;
    --text-muted-custom: #64748b;
    --sidebar-text: rgba(255, 255, 255, 0.65);
    --sidebar-text-active: #fec452;

    /* Layout Dimensions */
    --sidebar-width: 260px;
    --top-header-height: 70px;
    --transition-speed: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
```

---

### Project Goal: Care. Connect. Recover.
