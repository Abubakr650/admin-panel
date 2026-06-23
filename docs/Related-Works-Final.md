# Chapter 2: Related Works

## 2.1 Introduction

In this chapter, we review existing dental clinic management systems that have been previously developed. We examine their core features, technical stacks, and limitations. These systems served as a foundation for identifying the gaps that our proposed system aims to address.

---

## 2.1.1 System 1: Basic Dental Clinic Management System (C# / ASP.NET)

### Introduction

This system was developed using **C#** and **ASP.NET** with **Microsoft SQL Server 2008** as the backend database. It provides basic functionality for managing clinic operations, including the ability to add patient records and schedule appointments. However, it lacks several key features that are expected in modern healthcare management systems.

The system has no capability to delete patient records, suffers from known stability issues, and does not implement any form of role-based access control, which poses significant security and data integrity risks.

### Advantages

1. **Basic Patient Record Management:** Allows clinic staff to add and view patient records and appointment information.
2. **Appointment Scheduling:** Provides a simple mechanism for scheduling patient visits.
3. **Billing Support:** Includes basic billing and invoicing functionality for tracking patient payments.
4. **Familiar Technology Stack:** Built on .NET and SQL Server, technologies widely used in enterprise environments.
5. **Structured Data Storage:** Utilizes a relational database, ensuring structured and queryable data.

### Disadvantages

1. **No Record Deletion:** The system does not allow deletion of patient records, leading to data accumulation and management difficulties.
2. **No Role-Based Access Control (RBAC):** All users have the same level of access, creating a serious security vulnerability.
3. **Stability Issues:** The system is prone to crashes and unexpected behavior under normal clinic workloads.
4. **Licensing Costs:** Microsoft SQL Server requires paid licensing for production deployments, increasing operational costs.
5. **No Cross-Platform Support:** Built exclusively for Windows environments with no support for Linux or macOS.
6. **Outdated Technology:** SQL Server 2008 is no longer officially supported by Microsoft, exposing the system to unpatched security vulnerabilities.
7. **No Modern UI:** The interface is not responsive and does not adapt to different screen sizes or devices.

---

## 2.2 System 2: Advanced Dental Clinic Management System (C# / SQL Server)

### Introduction

This system was also developed using **C#** with **Microsoft SQL Server** as the backend. Compared to the first system, it provides a more advanced feature set, including full CRUD operations (Create, Read, Update, Delete) for patient records, appointment management, treatment logs, and financial transactions.

The system targets medium-to-large dental practices and offers a more organized workflow for clinic staff, including dentists, receptionists, and administrative personnel.

### Advantages

1. **Full CRUD Operations:** Supports adding, updating, and deleting patient records, appointments, and financial data.
2. **Appointment Management:** Provides comprehensive scheduling including rescheduling and cancellation workflows.
3. **Treatment Record Tracking:** Dentists can log and retrieve detailed treatment histories per patient.
4. **Invoice and Payment Management:** Automates billing processes, enabling efficient tracking of invoices and payments.
5. **Scalability Potential:** Architecture allows for future expansion of modules such as imaging or notifications.

### Disadvantages

1. **No AI Integration:** The system does not leverage artificial intelligence for any diagnostic or analytical purpose, such as X-ray image analysis.
2. **No Pharmacy or Warehouse Module:** There is no functionality for managing medications, drug batches, expiry dates, or warehouse inventory.
3. **No Radiology Module:** The system does not support uploading or storing dental X-ray images.
4. **No Orthodontics Module:** Orthodontic case tracking and session management are not supported.
5. **No Multi-Currency Support:** Financial transactions are limited to a single currency with no exchange rate management.
6. **No Role-Based Access Control:** Access control is rudimentary and does not differentiate between roles such as doctor, pharmacist, accountant, or receptionist.
7. **Desktop-Only Application:** The system is not web-based, limiting access to a single machine and preventing remote use.
8. **No Cloud Storage Integration:** Patient images and documents cannot be stored in the cloud, limiting scalability and backup capabilities.

---

## 2.3 System 3: Hybrid Dental Clinic Management System (C# / Python Django / SQLite)

### Introduction

This system was developed as a hybrid solution combining a **C# Windows Forms desktop application** and a **Python Django web application**, using **SQLite** as the backend database and **REST API** for communication between the two components. The system was designed to support both local desktop usage and web-based access, targeting multi-environment clinic operations.

The database schema (as shown in the ERD diagram) comprises the following core tables: **Patients**, **Dentists**, **Appointments**, **AppointmentStatus**, **MedicalRecords**, **Invoices**, **PaymentStatus**, **Operations**, and **OperationTypes**. All tables use integer primary keys (`int pk`) and standard `nvarchar` / `decimal` field types.

### Advantages

1. **Hybrid Architecture:** Combines a desktop (WinForms C#) and a web application (Django), offering flexibility for different clinic environments.
2. **Medical Record Tracking:** Supports diagnostic entries linked to patient records including diagnosis and treatment fields.
3. **Operation & Procedure Management:** Tracks dental operations with associated costs and payment status per patient.
4. **Appointment Status Tracking:** Uses a dedicated `AppointmentStatus` lookup table for tracking appointment states.
5. **REST API Integration:** Exposes endpoints enabling communication between the desktop and web layers.
6. **Multi-Technology Stack:** Demonstrates integration of multiple technologies (C#, Python, Django, Bootstrap, JavaScript).

### Disadvantages

1. **Weak Database Design:** Uses plain integer IDs instead of UUIDs, which poses scalability and uniqueness risks in distributed environments.
2. **SQLite Limitations:** SQLite is not suitable for concurrent multi-user production environments due to write-locking limitations.
3. **No Role-Based Access Control:** The system does not define differentiated roles (e.g., doctor, receptionist, accountant, pharmacist), meaning all users share the same access level.
4. **No Pharmacy or Warehouse Module:** Drug inventory, batch tracking, expiry date monitoring, and warehouse management are entirely absent.
5. **No Radiology Module:** There is no support for uploading, storing, or analyzing dental X-ray images.
6. **No Orthodontics Module:** Orthodontic case management and session tracking are not included.
7. **No AI Integration:** The system does not incorporate any machine learning or AI-based features such as diagnostic assistance or automated analysis.
8. **No Multi-Currency Support:** Financial transactions are single-currency with no exchange rate management.
9. **No Cloud Storage:** Patient images or documents cannot be stored on cloud platforms; the system relies on local file storage only.
10. **No QR Code Support:** Inventory or patient lookup via QR code is not supported.
11. **Complex Maintenance:** Managing two separate technology stacks (C# and Python) significantly increases maintenance complexity and development overhead.
12. **No Soft Delete:** Records appear to be hard-deleted with no archiving or recovery mechanism.

---

## 2.4 Comparison Table

| Feature | System 1 (C# / ASP.NET) | System 2 (C# / SQL Server) | System 3 (C# + Django / SQLite) | **Our System (Laravel / PHP)** |
|---|---|---|---|---|
| Web-Based | ✅ Partial | ❌ Desktop Only | ✅ Partial (Hybrid) | ✅ Fully Web-Based |
| Role-Based Access Control | ❌ | ❌ | ❌ | ✅ 7 Roles |
| Patient Management | ✅ Basic | ✅ Full CRUD | ✅ Full CRUD | ✅ Full CRUD + UUID |
| Appointment Management | ✅ Basic | ✅ Advanced | ✅ With status tracking | ✅ With follow-up support |
| Medical Records | ❌ | ✅ Basic | ✅ Diagnosis + Treatment | ✅ Per module |
| Billing & Invoicing | ✅ Basic | ✅ Advanced | ✅ With payment status | ✅ Multi-currency |
| Multi-Currency Support | ❌ | ❌ | ❌ | ✅ With exchange rates |
| Pharmacy Module | ❌ | ❌ | ❌ | ✅ Batches + Expiry alerts |
| Warehouse Module | ❌ | ❌ | ❌ | ✅ QR Code support |
| Supplier Management | ❌ | ❌ | ❌ | ✅ |
| Radiology Module | ❌ | ❌ | ❌ | ✅ X-Ray image management |
| AI X-Ray Analysis | ❌ | ❌ | ❌ | ✅ Integrated AI analysis |
| Orthodontics Module | ❌ | ❌ | ❌ | ✅ Cases + Sessions |
| Cloud Storage (S3) | ❌ | ❌ | ❌ | ✅ |
| QR Code Scanning | ❌ | ❌ | ❌ | ✅ |
| Soft Delete / Archive | ❌ | ❌ | ❌ | ✅ All modules |
| UUID Primary Keys | ❌ | ❌ | ❌ | ✅ |
| REST API | ❌ | ❌ | ✅ Between components | ✅ Full backend API |
| Open Source / Free Stack | ❌ (Licensing) | ❌ (Licensing) | ✅ Partial | ✅ Fully open source |
| Cross-Platform | ❌ | ❌ | ✅ Partial | ✅ Any OS / Browser |

---

## 2.5 Summary

The three reviewed systems demonstrate that existing dental clinic management solutions are largely limited in scope. System 1 and System 2 are desktop-centric applications built on Microsoft technologies, both lacking role-based access control, modern security practices, and any specialized clinical modules beyond basic appointment and billing management. System 3 represents a more ambitious hybrid approach combining C# and Django, and introduces structured operation tracking and a REST API layer; however, it still suffers from critical gaps including no pharmacy or warehouse management, no radiology or orthodontics support, no AI integration, no cloud storage, and no multi-currency handling.

Our proposed system, built on **Laravel (PHP)** with a modern web stack, directly addresses all of these shortcomings across all three reviewed systems. It introduces a comprehensive, modular, and scalable platform that unifies clinical, financial, pharmaceutical, radiological, and orthodontic workflows under a single **role-aware system with 7 distinct roles**, while integrating cutting-edge features such as **AI-powered X-ray analysis**, **cloud storage (S3)**, **QR code-based inventory management**, **multi-currency billing with exchange rates**, and **UUID-based data integrity** across all modules.
