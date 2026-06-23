# Chapter 2: Related Works

## 2.1 Introduction

This chapter reviews three existing dental clinic management systems that have been analyzed and compared with the proposed system. The first two systems are well-established commercial and open-source solutions widely deployed in real clinical environments. The third system is an academic project whose full source code was examined during this study. This comparative analysis highlights the gaps that our system is designed to address.

---

## 2.1.1 System 1: Dentrix (Henry Schein One)

### Introduction

**Dentrix** is one of the most widely used dental practice management systems in the world, developed and maintained by **Henry Schein One**. It was first released in the **1980s** and has since grown into a comprehensive commercial platform used by hundreds of thousands of dental professionals across North America and internationally. Dentrix offers both on-premise (desktop) and cloud-connected versions and integrates tightly with dental imaging hardware, insurance billing systems, and third-party clinical tools.

The system targets large and mid-sized dental practices, covering the full lifecycle of patient management from initial registration through to billing, treatment planning, and follow-up care. Its enterprise-grade architecture supports multi-chair and multi-location clinics.

**Website:** [https://www.dentrix.com](https://www.dentrix.com)
**Developer:** Henry Schein One (USA)
**Pricing:** Commercial — paid licensing and annual subscription

### Advantages

1. **Mature and Battle-Tested:** With over 40 years of development, Dentrix is a highly stable and well-optimized system trusted by large dental enterprises.
2. **Comprehensive Patient Lifecycle Management:** Supports the complete patient journey including scheduling, treatment planning, clinical notes, referrals, and recall management.
3. **Insurance & Billing Integration:** Provides direct electronic insurance claim submission (EDI), real-time eligibility verification, and automated payment posting.
4. **Imaging Integration:** Integrates with major dental X-ray and intraoral camera hardware (e.g., Carestream, Dexis) for in-system image viewing.
5. **Multi-Location Support:** Capable of managing multiple clinic branches under a single administrative account.
6. **Reporting and Analytics:** Advanced built-in reporting tools for financial performance, appointment analysis, and patient retention metrics.
7. **Training and Support:** Backed by a large professional support network, training programs, and a dedicated user community.

### Disadvantages

1. **High Cost:** Dentrix requires expensive upfront licensing and ongoing annual subscription fees, making it inaccessible for small clinics or clinics in developing countries.
2. **Windows-Only Desktop Client:** The traditional Dentrix application is restricted to Windows environments, with no native Linux or macOS support.
3. **No Open Pharmacy Module:** Dentrix does not include dedicated pharmacy inventory management, batch tracking, or drug expiry monitoring.
4. **No Warehouse Management:** There is no module for managing non-pharmaceutical clinic supply inventory (e.g., dental materials, tools).
5. **No AI Diagnostic Analysis:** While imaging is supported, Dentrix does not include built-in AI-powered X-ray analysis or automated diagnostic suggestions.
6. **No Orthodontic-Specific Module:** Orthodontic case management and session tracking are handled as general treatment notes rather than a dedicated structured module.
7. **No Multi-Currency or Exchange Rate Support:** Dentrix is designed for the North American market and does not natively support multi-currency billing or exchange rate management.
8. **Vendor Lock-In:** Being a closed commercial platform, clinics are fully dependent on Henry Schein One for updates, pricing changes, and data portability.

---

## 2.2 System 2: Open Dental

### Introduction

**Open Dental** is a fully open-source dental practice management system developed by **Open Dental Software**, based in Oregon, USA. First released in **2003**, it has grown into one of the most widely adopted open-source dental systems globally, used by thousands of dental practices in the United States, Canada, and internationally. The system is built on **C#** and **MySQL**, and its source code is publicly available under an open-source license, allowing clinics and developers to customize and extend it freely.

Open Dental is designed for general dental practices and supports a broad range of clinical and administrative workflows. The system is desktop-based (Windows) but can be configured to run over a local network for multi-workstation environments.

**Website:** [https://www.opendental.com](https://www.opendental.com)
**Developer:** Open Dental Software (USA)
**Pricing:** Free (open-source) with optional paid support plans

### Advantages

1. **Open Source:** Full access to source code allows complete customization and flexibility without vendor lock-in.
2. **No Licensing Fees:** The core application is available at no cost, significantly reducing operational expenses for small and medium clinics.
3. **Full CRUD Patient Management:** Supports complete management of patient demographics, medical history, treatment plans, and clinical notes.
4. **Appointment Scheduling:** Feature-rich scheduling module with color-coded appointment views, operatory management, and reminder tools.
5. **Insurance and Billing:** Handles insurance plans, claim submission, payment tracking, and EOB (Explanation of Benefits) processing.
6. **Imaging Support:** Integrates with dental imaging software and supports X-ray image attachment to patient records.
7. **Large Community:** Backed by an active developer and user community with extensive documentation and forum support.
8. **Prescription Management:** Basic prescription writing and tracking functionality is included.

### Disadvantages

1. **Windows Desktop Only:** Open Dental is a Windows-only desktop application; there is no native web interface or mobile access.
2. **Complex Setup:** Initial configuration and network setup can be technically challenging for non-IT clinic staff.
3. **No AI Integration:** The system does not include any AI-powered diagnostic or analytical features.
4. **No Dedicated Pharmacy/Warehouse Module:** While prescriptions are supported, there is no inventory management for drug batches, expiry tracking, or warehouse supplies.
5. **No Orthodontic Module:** Orthodontic case progression, session tracking, and wire change logs are not natively supported as a specialized module.
6. **No Multi-Currency Support:** The system is designed exclusively for USD billing with no exchange rate conversion capability.
7. **No Cloud Storage:** Patient images and files are stored locally on the server with no native integration with cloud storage services such as Amazon S3.
8. **No QR Code Support:** There is no QR code-based workflow for patient check-in, inventory scanning, or equipment tracking.
9. **Outdated UI:** The user interface has not been significantly modernized and lacks the responsiveness and aesthetics expected of modern web applications.

---

## 2.3 System 3: Academic Dental Clinic Management System (C# WinForms + Python Django / SQLite)

### Introduction

This system is an academic dental clinic management solution developed as a graduation project. It employs a **hybrid architecture** combining a **C# Windows Forms desktop application** and a **Python Django web application**, connected via a **REST API**. The backend database used is **SQLite**, and the frontend web layer is built using **HTML, CSS, JavaScript, and Bootstrap**.

The database schema — verified through full source code review — includes the following core tables: `Patients`, `Dentists`, `Appointments`, `AppointmentStatus`, `MedicalRecords`, `Invoices`, `PaymentStatus`, `Operations`, and `OperationTypes`. All relationships use integer primary keys (`int pk`) with `nvarchar` and `decimal` field types for clinical and financial data. The system aims to support dental clinic operations across both local and web-accessible environments.

### Advantages

1. **Hybrid Architecture:** Provides both a desktop WinForms interface and a Django web portal, offering multi-environment accessibility.
2. **Medical Record Tracking:** Supports diagnostic entries linked to patient records, including diagnosis text and treatment details.
3. **Dental Operation Management:** Tracks procedures and operations performed per patient with associated costs and payment statuses.
4. **Appointment Status Tracking:** Uses a dedicated `AppointmentStatus` lookup table to manage the lifecycle stages of appointments.
5. **REST API Layer:** Exposes a RESTful API enabling communication between the desktop client and the web application.
6. **Low Infrastructure Cost:** SQLite requires no separate database server installation, reducing deployment complexity in limited environments.

### Disadvantages

1. **Weak Database Design:** Uses plain auto-incremented integer IDs (`int pk`) instead of UUIDs, creating potential conflicts and scalability limitations in distributed or replicated setups.
2. **SQLite Not Production-Ready:** SQLite is unsuitable for concurrent multi-user production environments due to its file-level write-locking mechanism.
3. **No Role-Based Access Control (RBAC):** The system does not define differentiated permissions for roles such as doctor, receptionist, pharmacist, or accountant — all users operate at the same access level.
4. **No Pharmacy Module:** Drug inventory, batch tracking, expiry date monitoring, and supplier management are entirely absent from the system.
5. **No Warehouse Module:** There is no functionality for managing non-pharmaceutical clinic supplies or equipment inventory.
6. **No Radiology Module:** The system does not support uploading, categorizing, or analyzing dental X-ray images in a structured way.
7. **No AI Integration:** No machine learning or AI-based features are present, such as automated X-ray diagnosis or intelligent appointment suggestions.
8. **No Orthodontics Module:** Orthodontic case progression, bracket placement, wire change sessions, and treatment timelines are not supported.
9. **No Multi-Currency Support:** Financial transactions are handled in a single currency with no provision for exchange rates.
10. **No Cloud Storage:** All patient data and files reside on local storage only; no integration with cloud services such as AWS S3 exists.
11. **No QR Code Support:** QR-based workflows for inventory lookup or patient identification are not implemented.
12. **High Maintenance Complexity:** Managing two separate technology stacks (C# and Python/Django) significantly increases the complexity of ongoing development and deployment.
13. **No Soft Delete/Archive:** Records appear to be permanently deleted with no archiving or recovery mechanism built into the schema.

---

## 2.4 System 4: DentalCare Pro (Academic Desktop Application)

### Introduction

**DentalCare Pro** is another academic dental clinic management system, built as a standalone desktop application to run locally within a clinic without requiring a constant internet connection. The system is designed with a focus on simplicity, local data encryption, and offline speed.

Based on the provided entity-relationship diagrams (ERDs) and database schemas, the system relies on a localized relational database. The core tables include `Patient`, `Employee`, `Services`, `Users`, `User_Setting`, `Registration`, `Invoice`, `Invoice_Details`, `Returns`, and `Blood_Type`. Similar to System 3, it relies on standard auto-incrementing integers (`int`) for primary keys. The system introduces basic role separation natively, offering distinct access for Doctors, Employees, and Managers.

### Advantages

1. **Local Data Encryption:** Emphasizes local security by encrypting data stored directly on the clinic's machines.
2. **Offline Operations:** Operates entirely independent of an internet connection, ensuring high responsiveness.
3. **Defined Roles:** The system explicitly defines access interfaces for distinct roles (Doctor, Employee, Manager/Admin).
4. **Dashboard & Alerts:** Includes a dashboard displaying vital statistics, calendars, and appointment reminders.
5. **PDF Export Support:** Generates and exports invoices and patient reports directly into PDF format.

### Disadvantages

1. **No Multi-User Cloud Synchronization:** The system lacks real-time multi-user synchronization across different devices over the internet.
2. **Desktop-Bound:** Doctors cannot monitor or access the system outside the clinic premises.
3. **No Cloud Backups:** Data is saved locally without automatic cloud syncing, causing severe risk of data loss upon hardware failure.
4. **Weak Key Architecture:** Uses standard integer IDs rather than robust UUIDs, creating challenges if data ever needs to be merged or synced.
5. **No Advanced Clinical Modules:** The database schema reveals the complete absence of dedicated modules for Pharmacy, Warehouse management, Radiology, and Orthodontics.
6. **No AI Integration:** Lacks intelligent diagnostic features or AI analysis capabilities.
7. **No Multi-Currency Support:** Billing is strictly single-currency with no exchange rate mechanisms.

---

## 2.5 Comparison Table

| Feature | Dentrix | Open Dental | System 3 (C# / Django) | System 4 (DentalCare Pro) | **Our System (Laravel / PHP)** |
|---|---|---|---|---|---|
| Web-Based | ⚠️ Partial (Cloud Add-on) | ❌ Desktop Only | ⚠️ Partial (Hybrid) | ❌ Desktop Only | ✅ Fully Web-Based |
| Open Source / Code Access | ❌ Commercial | ✅ | ✅ | ✅ | ✅ |
| Role-Based Access Control | ✅ Basic | ✅ Basic | ❌ | ✅ Basic (3 Roles) | ✅ 7 Defined Roles |
| Patient Management | ✅ Advanced | ✅ Full CRUD | ✅ Full CRUD | ✅ Full CRUD | ✅ Full CRUD + UUID |
| Appointment Scheduling | ✅ Advanced | ✅ Advanced | ✅ With status | ✅ With calendar | ✅ With follow-up (parent/child) |
| Medical Records | ✅ | ✅ | ✅ | ✅ | ✅ Per module |
| Billing & Invoicing | ✅ Insurance + EDI | ✅ Insurance + Claims | ✅ Payment status | ✅ Print to PDF | ✅ Multi-currency |
| Multi-Currency Support | ❌ | ❌ | ❌ | ❌ | ✅ With exchange rates |
| Pharmacy / Drug Inventory | ❌ | ⚠️ Prescriptions only | ❌ | ❌ | ✅ Batches + Expiry alerts |
| Warehouse Module | ❌ | ❌ | ❌ | ❌ | ✅ QR Code support |
| Supplier Management | ❌ | ❌ | ❌ | ❌ | ✅ |
| Radiology / X-Ray Module | ⚠️ Hardware-linked | ⚠️ Basic attachment | ❌ | ❌ | ✅ Structured module |
| AI X-Ray Analysis | ❌ | ❌ | ❌ | ❌ | ✅ Integrated AI analysis |
| Orthodontics Module | ❌ | ❌ | ❌ | ❌ | ✅ Cases + Sessions |
| Cloud Storage (S3) | ⚠️ Limited | ❌ | ❌ | ❌ | ✅ |
| QR Code Scanning | ❌ | ❌ | ❌ | ❌ | ✅ |
| Soft Delete / Archive | ✅ | ⚠️ Partial | ❌ | ❌ | ✅ All modules |
| UUID Primary Keys | ❌ | ❌ | ❌ | ❌ | ✅ |
| REST API | ⚠️ Paid Add-on | ⚠️ Limited | ✅ Internal only | ❌ | ✅ Full backend |
| Cross-Platform | ❌ Windows only | ❌ Windows only | ⚠️ Partial | ❌ Windows only | ✅ Any OS / Browser |
| Licensing Cost | ❌ Expensive | ✅ Free | ✅ Free | ✅ Free | ✅ Free |

---

## 2.6 Summary

The four systems reviewed in this chapter represent a broad spectrum of dental clinic management solutions — from a leading commercial enterprise platform (Dentrix) to a free open-source system (Open Dental), to two locally developed academic systems (a C#/Django hybrid and the strictly offline DentalCare Pro desktop app). Despite their differences in scale and maturity, all four share a common set of critical limitations when measured against the demands of a modern, comprehensive dental practice management platform.

Dentrix, while powerful and mature, is prohibitively expensive, locked to Windows, and lacks specialized modules for pharmacy inventory, warehouse management, AI diagnostics, orthodontics, and multi-currency support. Open Dental, though free and highly customizable, suffers from the same architectural limitations: it is desktop-only, lacks cloud integration, and offers no dedicated pharmacy, radiology, or orthodontic modules. The academic systems, while solving isolated problems like local encryption or REST APIs, are constrained by weak integer-based database scaling, extreme dependency on local hardware (high risk of data loss), and the complete lack of modern clinical modules.

Our proposed system, built on **Laravel (PHP)** with a modern, fully web-based stack, directly addresses all identified shortcomings across all four reviewed systems. It delivers a comprehensive, modular platform that integrates clinical operations, financial management, pharmaceutical inventory, warehouse tracking, AI-powered radiology analysis, and multi-session orthodontic care — all governed by a structured **role-based access control system with 7 distinct roles**, and deployed on a scalable, cloud-connected infrastructure with **UUID-based data integrity**, **Amazon S3 storage**, and **QR code workflow support**.
