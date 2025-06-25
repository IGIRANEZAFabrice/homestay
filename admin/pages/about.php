<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit About Page - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --secondary-color: #eebf63;
            --accent-color: #2e335a;
            --text-color: #333;
            --light-bg: #f8f9fa;
            --border-radius: 10px;
            --box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
            --success-color: #4CAF50;
            --danger-color: #f44336;
            --warning-color: #ff9800;
            --info-color: #2196F3;
        }
        
        .admin-container {
            margin-left: 260px; /* Default margin for visible sidebar */
            padding: 2rem;
            transition: all 0.3s ease;
            background-color: #f8fafd;
            min-height: 100vh;
            position: relative;
        }
        
        @media (max-width: 900px) {
            .admin-container {
                margin-left: 0;
                padding: 1.5rem;
            }
        }
        
        .sidebar.hide + .admin-container {
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }
        
        .sidebar.small + .admin-container {
            margin-left: 70px;
            transition: margin-left 0.3s ease;
        }
        
        h1 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        h1::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 60px;
            background: var(--secondary-color);
            border-radius: 3px;
        }
        
        .form-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            margin-bottom: 2.5rem;
            padding: 2rem;
            transition: var(--transition);
            border: 1px solid rgba(0,0,0,0.03);
            position: relative;
            overflow: hidden;
        }
        
        .form-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
            opacity: 0.7;
        }
        
        .form-section:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transform: translateY(-3px);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.8rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            position: relative;
        }
        
        .section-header::after {
            content: "";
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 80px;
            height: 2px;
            background: linear-gradient(to right, var(--secondary-color), transparent);
        }
        
        .section-header h2 {
            font-size: 1.3rem;
            color: var(--primary-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            font-weight: 600;
        }
        
        .section-header h2 i {
            color: var(--secondary-color);
            font-size: 1.2rem;
            background: rgba(238, 191, 99, 0.1);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .section-header:hover h2 i {
            transform: scale(1.1);
            background: rgba(238, 191, 99, 0.2);
        }
        
        .section-description {
            color: #6c757d;
            margin-bottom: 1.8rem;
            font-size: 0.95rem;
            line-height: 1.6;
            border-left: 3px solid var(--secondary-color);
            padding-left: 1rem;
            background: rgba(238, 191, 99, 0.05);
            padding: 0.8rem 1rem;
            border-radius: 0 8px 8px 0;
        }
        
        .form-group {
            margin-bottom: 1.8rem;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.7rem;
            font-weight: 500;
            color: #495057;
            font-size: 0.95rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-group label::before {
            content: "";
            display: inline-block;
            width: 6px;
            height: 6px;
            background-color: var(--secondary-color);
            border-radius: 50%;
            margin-right: 0.3rem;
            opacity: 0.7;
        }
        
        .form-group:hover label {
            color: var(--primary-color);
        }
        
        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: var(--border-radius);
            transition: var(--transition);
            font-size: 0.95rem;
            background-color: #f9f9f9;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.02);
        }
        
        .form-group input[type="text"]:hover,
        .form-group textarea:hover {
            border-color: rgba(67, 97, 238, 0.3);
            background-color: #fff;
        }
        
        .form-group input[type="text"]:focus,
        .form-group textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
            outline: none;
            background-color: #fff;
        }
        
        .form-group textarea {
            min-height: 120px;
            line-height: 1.6;
            resize: vertical;
        }
        
        .image-preview {
            margin-top: 1rem;
            border-radius: var(--border-radius);
            overflow: hidden;
            max-width: 300px;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            position: relative;
            transition: var(--transition);
        }
        
        .image-preview:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .image-preview img {
            width: 100%;
            height: 180px;
            display: block;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .image-preview:hover img {
            transform: scale(1.03);
        }
        
        .image-preview::after {
            content: "Preview";
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.6);
            color: white;
            text-align: center;
            padding: 0.4rem;
            font-size: 0.8rem;
            letter-spacing: 1px;
            opacity: 0;
            transform: translateY(100%);
            transition: var(--transition);
        }
        
        .image-preview:hover::after {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Values Grid Styling */
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.8rem;
            margin-top: 2rem;
        }
        
        .value-card {
            background-color: white;
            border-radius: 12px;
            padding: 1.8rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            transition: all 0.4s ease;
            border: 1px solid rgba(0,0,0,0.03);
            position: relative;
            overflow: hidden;
        }
        
        .value-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
            opacity: 0.7;
            transition: all 0.4s ease;
        }
        
        .value-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transform: translateY(-7px);
            border-color: rgba(0,0,0,0.08);
        }
        
        .value-card:hover::before {
            width: 6px;
            opacity: 1;
        }
        
        .value-card .form-group:first-child {
            margin-bottom: 1.2rem;
        }
        
        .value-card .form-group:first-child input {
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--primary-color);
            border-bottom: 2px solid rgba(0,0,0,0.05);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        .value-card .form-group:first-child input:focus {
            border-bottom-color: var(--secondary-color);
        }
        
        /* Team Cards Styling */
        .team-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .team-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
            transition: all 0.4s ease;
            position: relative;
            border: 1px solid rgba(0,0,0,0.03);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .team-card:hover {
            box-shadow: 0 12px 30px rgba(0,0,0,0.1);
            transform: translateY(-8px);
            border-color: rgba(0,0,0,0.08);
        }
        
        .team-card-image {
            height: 220px;
            overflow: hidden;
            position: relative;
        }
        
        .team-card-image::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50%;
            background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
            opacity: 0;
            transition: all 0.4s ease;
        }
        
        .team-card:hover .team-card-image::after {
            opacity: 1;
        }
        
        .team-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .team-card:hover .team-card-image img {
            transform: scale(1.08);
        }
        
        .team-card-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .team-card-content h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.2rem;
            color: var(--primary-color);
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .team-card:hover .team-card-content h3 {
            color: var(--hover-color);
        }
        
        .team-card-content p.position {
            color: var(--secondary-color);
            margin: 0 0 1rem 0;
            font-weight: 500;
            font-size: 0.95rem;
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: rgba(238, 191, 99, 0.1);
            border-radius: 20px;
        }
        
        .team-card-content p.bio {
            color: #495057;
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.6;
            flex-grow: 1;
        }
        
        .team-card-actions {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            display: flex;
            gap: 0.8rem;
            z-index: 10;
        }
        
        .team-card-actions button {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .team-card-actions button:hover {
            transform: scale(1.15) rotate(5deg);
        }
        
        .team-card-actions button.edit {
            color: var(--primary-color);
        }
        
        .team-card-actions button.delete {
            color: #dc3545;
        }
        
        /* Button Styling */
        .btn-add {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }
        
        .btn-add:hover {
            background-color: var(--primary-hover);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 0.75rem 1.5rem;
            font-size: 0.95rem;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 0.75rem 1.5rem;
            font-size: 0.95rem;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            z-index: 1000;
            overflow-y: auto;
            opacity: 0;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(3px);
        }
        
        .modal.show {
            opacity: 1;
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 1.8rem;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.4s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .modal.show .modal-content {
            transform: translateY(0);
            opacity: 1;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.8rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            position: relative;
        }
        
        .modal-header::after {
            content: "";
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 80px;
            height: 2px;
            background: linear-gradient(to right, var(--primary-color), transparent);
        }
        
        .modal-header h2 {
            margin: 0;
            font-size: 1.3rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.7rem;
            font-weight: 600;
        }
        
        .modal-header h2 i {
            color: var(--secondary-color);
            font-size: 1.2rem;
            background: rgba(238, 191, 99, 0.1);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .modal-header:hover h2 i {
            transform: scale(1.1);
            background: rgba(238, 191, 99, 0.2);
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .close-modal {
            color: #aaa;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
            transition: var(--transition);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(0,0,0,0.03);
        }
        
        .close-modal:hover {
            color: var(--text-color);
            background: rgba(220, 53, 69, 0.1);
            transform: rotate(90deg);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .modal-content {
                margin: 10% auto;
                width: 95%;
                padding: 1.5rem;
            }
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .values-grid,
            .team-cards {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .form-actions button {
                width: 100%;
            }
            
            .modal-content {
                width: 95%;
                margin: 5% auto;
            }
        }
        
        .mt-2 {
            margin-top: 0.75rem;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .form-section h2 {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-section h2 i {
            color: var(--secondary-color);
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .team-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.8rem;
        }
        
        .team-card {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            background: white;
        }
        
        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        }
        
        .team-card-image {
            height: 220px;
            overflow: hidden;
        }
        
        .team-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .team-card:hover .team-card-image img {
            transform: scale(1.05);
        }
        
        .team-card-content {
            padding: 1.5rem;
        }
        
        .team-card-actions {
            position: absolute;
            top: 0.8rem;
            right: 0.8rem;
            display: flex;
            gap: 0.5rem;
            opacity: 0;
            transition: var(--transition);
        }
        
        .team-card:hover .team-card-actions {
            opacity: 1;
        }
        
        .btn-edit, .btn-delete {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: var(--transition);
        }
        
        .btn-edit {
            color: #3498db;
        }
        
        .btn-delete {
            color: #e74c3c;
        }
        
        .btn-edit:hover {
            background: #3498db;
            color: white;
            transform: scale(1.1);
        }
        
        .btn-delete:hover {
            background: #e74c3c;
            color: white;
            transform: scale(1.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
        }
        
        input[type="text"], textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        input[type="text"]:focus, textarea:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(238, 191, 99, 0.2);
            outline: none;
        }
        
        .btn-add, .btn-primary, .btn-secondary {
            border: none;
            border-radius: var(--border-radius);
            padding: 0.7rem 1.2rem;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.3px;
        }
        
        .btn-add::before, .btn-primary::before, .btn-secondary::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.6s ease;
        }
        
        .btn-add:hover::before, .btn-primary:hover::before, .btn-secondary:hover::before {
            left: 100%;
        }
        
        .btn-add, .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
        }
        
        .btn-add:hover, .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-hover), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }
        
        .btn-add:active, .btn-primary:active {
            transform: translateY(1px);
            box-shadow: 0 2px 3px rgba(67, 97, 238, 0.2);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268, #6c757d);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }
        
        .btn-secondary:active {
            transform: translateY(1px);
            box-shadow: 0 2px 3px rgba(108, 117, 125, 0.2);
        }
        
        .btn-add i, .btn-primary i, .btn-secondary i {
            font-size: 1rem;
            transition: transform 0.3s ease;
        }
        
        .btn-add:hover i, .btn-primary:hover i, .btn-secondary:hover i {
            transform: scale(1.2);
        }
        
        .form-actions {
            display: flex;
            gap: 1.2rem;
            margin-top: 2.5rem;
            padding-top: 1.8rem;
            border-top: 1px solid rgba(0,0,0,0.05);
            position: relative;
        }
        
        .form-actions::before {
            content: "";
            position: absolute;
            top: -1px;
            left: 0;
            width: 100px;
            height: 2px;
            background: linear-gradient(to right, var(--primary-color), transparent);
        }
        
        .btn-primary, .btn-secondary {
            padding: 0.8rem 1.8rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-primary {
            background: var(--secondary-color);
            color: var(--primary-color);
            border: none;
        }
        
        .btn-secondary {
            background: #f1f1f1;
            color: var(--text-color);
            border: 1px solid #ddd;
        }
        
        .btn-primary:hover {
            background: #e0aa40;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(238, 191, 99, 0.3);
        }
        
        .btn-secondary:hover {
            background: #e8e8e8;
            transform: translateY(-2px);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 1100;
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: white;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: modalFadeIn 0.3s ease;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .values-grid {
                grid-template-columns: 1fr;
            }
            
            .team-cards {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn-primary, .btn-secondary {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>
    <script>
    // Ensure sidebar is properly initialized
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('adminSidebar');
        if (sidebar) {
            // Make sure sidebar is visible and not small by default
            sidebar.classList.remove('hide');
            sidebar.classList.remove('small');
            console.log('About.php: Ensuring sidebar is fully visible');
        }
    });
    </script>
    <div class="admin-container">
        <h1>Edit About Page Content</h1>
        
        <form id="aboutEditForm" class="admin-form">
            <!-- Hero Section -->
            <div class="form-section">
                <div class="section-header">
                    <h2><i class="fas fa-image"></i> Hero Section</h2>
                </div>
                <div class="form-group">
                    <label for="heroTitle">Hero Title</label>
                    <input type="text" id="heroTitle" name="heroTitle" value="Our Story" placeholder="Enter hero title...">
                </div>
                <div class="form-group">
                    <label for="heroSubtitle">Hero Subtitle</label>
                    <input type="text" id="heroSubtitle" name="heroSubtitle" value="Discover the heart behind Virunga Homestay" placeholder="Enter hero subtitle...">
                </div>
            </div>

            <!-- Story Section -->
            <div class="form-section">
                <div class="section-header">
                    <h2><i class="fas fa-book-open"></i> Story Section</h2>
                </div>
                <div class="form-group">
                    <label for="storyTitle">Story Title</label>
                    <input type="text" id="storyTitle" name="storyTitle" value="Welcome to Virunga Homestay" placeholder="Enter story title...">
                </div>
                <div class="form-group">
                    <label for="storySubtitle">Story Subtitle</label>
                    <input type="text" id="storySubtitle" name="storySubtitle" value="Where Tradition Meets Modern Comfort" placeholder="Enter story subtitle...">
                </div>
                <div class="form-group">
                    <label for="storyDescription1">Story Description 1</label>
                    <textarea id="storyDescription1" name="storyDescription1" rows="4" placeholder="Enter first paragraph of your story...">Nestled in the heart of Rwanda, Virunga Homestay was born from a passion for sharing the rich cultural heritage and natural beauty of our country with the world. Our journey began with a simple vision: to create a space where travelers could experience authentic Rwandan hospitality while enjoying modern comforts.</textarea>
                </div>
                <div class="form-group">
                    <label for="storyDescription2">Story Description 2</label>
                    <textarea id="storyDescription2" name="storyDescription2" rows="4" placeholder="Enter second paragraph of your story...">What started as a small family project has grown into a beloved destination for travelers seeking an immersive cultural experience. Our homestay combines traditional Rwandan architecture with contemporary design, creating a unique atmosphere that honors our heritage while embracing modern living.</textarea>
                </div>
                <div class="form-group">
                    <label for="storyImage">Story Image URL</label>
                    <input type="text" id="storyImage" name="storyImage" value="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" placeholder="Enter image URL...">
                    <div class="image-preview mt-2">
                        <img id="storyImagePreview" src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="Story Image Preview">
                    </div>
                </div>
            </div>

            <!-- Mission Section -->
            <div class="form-section">
                <div class="section-header">
                    <h2><i class="fas fa-bullseye"></i> Mission Section</h2>
                </div>
                <div class="form-group">
                    <label for="missionTitle">Mission Title</label>
                    <input type="text" id="missionTitle" name="missionTitle" value="Our Mission" placeholder="Enter mission title...">
                </div>
                <div class="form-group">
                    <label for="missionDescription">Mission Description</label>
                    <textarea id="missionDescription" name="missionDescription" rows="4" placeholder="Enter mission description...">At Virunga Homestay, our mission is to provide an authentic cultural experience while promoting sustainable tourism that benefits local communities. We are committed to preserving Rwandan traditions, supporting local artisans, and creating meaningful connections between our guests and the community.</textarea>
                </div>
                <div class="form-group">
                    <label for="missionImage">Mission Image URL</label>
                    <input type="text" id="missionImage" name="missionImage" value="https://images.unsplash.com/photo-1528605248644-14dd04022da1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" placeholder="Enter image URL...">
                    <div class="image-preview mt-2">
                        <img id="missionImagePreview" src="https://images.unsplash.com/photo-1528605248644-14dd04022da1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="Mission Image Preview">
                    </div>
                </div>
            </div>

            <!-- Team Section -->
            <div class="form-section">
                <div class="section-header">
                    <h2><i class="fas fa-users"></i> Team Section</h2>
                    <button type="button" class="btn-add" onclick="openTeamModal()">
                        <i class="fas fa-plus"></i> Add Team Member
                    </button>
                </div>
                <p class="section-description">Add team members to showcase the people behind Virunga Homestay.</p>
                <div class="team-cards" id="teamCards">
                    <!-- Team cards will be dynamically added here -->
                </div>
            </div>

            <!-- Values Section -->
            <div class="form-section">
                <div class="section-header">
                    <h2><i class="fas fa-heart"></i> Values Section</h2>
                </div>
                <p class="section-description">Define the core values that guide Virunga Homestay.</p>
                <div class="values-grid">
                    <div class="value-card">
                        <div class="form-group">
                            <label for="value1Title">Value 1 Title</label>
                            <input type="text" id="value1Title" name="value1Title" value="Sustainability" placeholder="Enter value title...">
                        </div>
                        <div class="form-group">
                            <label for="value1Description">Value 1 Description</label>
                            <textarea id="value1Description" name="value1Description" rows="2" placeholder="Enter value description...">We are committed to eco-friendly practices and minimizing our environmental impact.</textarea>
                        </div>
                    </div>

                    <div class="value-card">
                        <div class="form-group">
                            <label for="value2Title">Value 2 Title</label>
                            <input type="text" id="value2Title" name="value2Title" value="Community" placeholder="Enter value title...">
                        </div>
                        <div class="form-group">
                            <label for="value2Description">Value 2 Description</label>
                        <textarea id="value2Description" name="value2Description" rows="2" placeholder="Enter value description...">We actively support and engage with our local community through various initiatives.</textarea>
                        </div>
                    </div>

                    <div class="value-card">
                        <div class="form-group">
                            <label for="value3Title">Value 3 Title</label>
                            <input type="text" id="value3Title" name="value3Title" value="Excellence" placeholder="Enter value title...">
                        </div>
                        <div class="form-group">
                            <label for="value3Description">Value 3 Description</label>
                            <textarea id="value3Description" name="value3Description" rows="2" placeholder="Enter value description...">We strive for excellence in every aspect of our service and guest experience.</textarea>
                        </div>
                    </div>

                    <div class="value-card">
                        <div class="form-group">
                            <label for="value4Title">Value 4 Title</label>
                            <input type="text" id="value4Title" name="value4Title" value="Heritage" placeholder="Enter value title...">
                        </div>
                        <div class="form-group">
                            <label for="value4Description">Value 4 Description</label>
                            <textarea id="value4Description" name="value4Description" rows="2" placeholder="Enter value description...">We preserve and celebrate Rwanda's rich cultural heritage through our activities.</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="form-section">
                <div class="section-header">
                    <h2><i class="fas fa-bullhorn"></i> CTA Section</h2>
                </div>
                <p class="section-description">Configure the call-to-action section that appears at the bottom of the About page.</p>
                <div class="form-group">
                    <label for="ctaTitle">CTA Title</label>
                    <input type="text" id="ctaTitle" name="ctaTitle" value="Experience Our Hospitality" placeholder="Enter CTA title...">
                </div>
                <div class="form-group">
                    <label for="ctaSubtitle">CTA Subtitle</label>
                    <input type="text" id="ctaSubtitle" name="ctaSubtitle" value="Join us for an unforgettable stay at Virunga Homestay" placeholder="Enter CTA subtitle...">
                </div>
                <div class="form-group">
                    <label for="ctaButtonText">CTA Button Text</label>
                    <input type="text" id="ctaButtonText" name="ctaButtonText" value="Book Now" placeholder="Enter button text...">
                </div>
                <div class="form-group">
                    <label for="ctaButtonLink">CTA Button Link</label>
                    <input type="text" id="ctaButtonLink" name="ctaButtonLink" value="/booking.html" placeholder="Enter button link...">
                </div>
                <div class="form-group">
                    <label for="ctaBackgroundImage">CTA Background Image URL</label>
                    <input type="text" id="ctaBackgroundImage" name="ctaBackgroundImage" value="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" placeholder="Enter background image URL...">
                    <div class="image-preview mt-2">
                        <img id="ctaBackgroundImagePreview" src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="CTA Background Image Preview">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                <button type="button" class="btn-secondary" onclick="previewChanges()"><i class="fas fa-eye"></i> Preview Changes</button>
            </div>
        </form>
    </div>

    <!-- Team Member Modal -->
    <div id="teamModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-plus"></i> Add New Team Member</h2>
                <button class="close-modal" onclick="closeTeamModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="teamMemberForm">
                    <div class="form-group">
                        <label for="memberName">Name</label>
                        <input type="text" id="memberName" name="memberName" placeholder="Enter team member name..." required>
                    </div>
                    <div class="form-group">
                        <label for="memberPosition">Position</label>
                        <input type="text" id="memberPosition" name="memberPosition" placeholder="Enter team member position..." required>
                    </div>
                    <div class="form-group">
                        <label for="memberBio">Bio</label>
                        <textarea id="memberBio" name="memberBio" rows="3" placeholder="Enter team member bio..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="memberImage">Image URL</label>
                        <input type="text" id="memberImage" name="memberImage" placeholder="Enter image URL..." required onchange="previewMemberImage(this.value)">
                        <div class="image-preview mt-2" id="memberImagePreviewContainer" style="display: none;">
                            <img id="memberImagePreview" src="" alt="Team Member Image Preview">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Team Member</button>
                        <button type="button" class="btn-secondary" onclick="closeTeamModal()"><i class="fas fa-times"></i> Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Notification Styling -->
    <style>
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 9999;
            overflow: hidden;
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s ease;
            max-width: 350px;
            background: white;
            border-left: 4px solid var(--primary-color);
        }
        
        .notification.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .notification.success {
            border-left-color: var(--success-color);
        }
        
        .notification.error {
            border-left-color: var(--danger-color);
        }
        
        .notification-content {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            gap: 12px;
        }
        
        .notification-content i {
            font-size: 1.2rem;
        }
        
        .notification.success i {
            color: var(--success-color);
        }
        
        .notification.error i {
            color: var(--danger-color);
        }
        
        .notification-content span {
            font-size: 0.95rem;
            color: #333;
        }
        
        /* Button success state */
        .btn-primary.success-state, .btn-secondary.success-state {
            background: var(--success-color);
            color: white;
        }
        
        /* Hover effect for image previews */
        .image-preview.hover-effect {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
    </style>
    
    <script>
        // Initialize image previews and animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to form sections
            animateFormSections();
            
            // Set up image preview for story image
            document.getElementById('storyImage').addEventListener('change', function() {
                animateImagePreview('storyImagePreview', this.value);
            });
            
            // Set up image preview for mission image
            document.getElementById('missionImage').addEventListener('change', function() {
                animateImagePreview('missionImagePreview', this.value);
            });
            
            // Set up image preview for CTA background image
            document.getElementById('ctaBackgroundImage').addEventListener('change', function() {
                animateImagePreview('ctaBackgroundImagePreview', this.value);
            });
            
            // Add hover effects to image previews
            document.querySelectorAll('.image-preview').forEach(preview => {
                preview.addEventListener('mouseenter', function() {
                    this.classList.add('hover-effect');
                });
                preview.addEventListener('mouseleave', function() {
                    this.classList.remove('hover-effect');
                });
            });
        });
        
        // Animate form sections on load
        function animateFormSections() {
            const sections = document.querySelectorAll('.form-section');
            sections.forEach((section, index) => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                section.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                
                setTimeout(() => {
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                }, 100 * (index + 1));
            });
        }
        
        // Animate image preview changes
        function animateImagePreview(previewId, url) {
            const previewImg = document.getElementById(previewId);
            
            // Fade out
            previewImg.style.transition = 'opacity 0.3s ease';
            previewImg.style.opacity = '0';
            
            // Change source and fade in
            setTimeout(() => {
                previewImg.src = url;
                previewImg.style.opacity = '1';
            }, 300);
        }
        
        // Preview member image in modal with animation
        function previewMemberImage(url) {
            const previewContainer = document.getElementById('memberImagePreviewContainer');
            const previewImg = document.getElementById('memberImagePreview');
            
            if (url && url.trim() !== '') {
                // If container is hidden, show it first
                if (previewContainer.style.display === 'none') {
                    previewContainer.style.display = 'block';
                    previewImg.style.opacity = '0';
                    previewImg.src = url;
                    
                    setTimeout(() => {
                        previewImg.style.transition = 'opacity 0.5s ease';
                        previewImg.style.opacity = '1';
                    }, 50);
                } else {
                    // If already visible, fade transition
                    previewImg.style.transition = 'opacity 0.3s ease';
                    previewImg.style.opacity = '0';
                    
                    setTimeout(() => {
                        previewImg.src = url;
                        previewImg.style.opacity = '1';
                    }, 300);
                }
            } else {
                // Fade out before hiding
                previewImg.style.transition = 'opacity 0.3s ease';
                previewImg.style.opacity = '0';
                
                setTimeout(() => {
                    previewContainer.style.display = 'none';
                }, 300);
            }
        }
        
        document.getElementById('aboutEditForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state on save button
            const saveButton = this.querySelector('button[type="submit"]');
            const originalText = saveButton.innerHTML;
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveButton.disabled = true;
            
            // Simulate saving (would be replaced with actual AJAX call)
            setTimeout(() => {
                saveButton.innerHTML = '<i class="fas fa-check"></i> Saved!';
                saveButton.classList.add('success-state');
                
                // Show success notification
                showNotification('Changes saved successfully!', 'success');
                
                // Reset button after delay
                setTimeout(() => {
                    saveButton.innerHTML = originalText;
                    saveButton.disabled = false;
                    saveButton.classList.remove('success-state');
                }, 1500);
            }, 1500);
        });

        function previewChanges() {
            // Show loading state
            const previewButton = document.querySelector('button.btn-secondary');
            const originalText = previewButton.innerHTML;
            previewButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading Preview...';
            previewButton.disabled = true;
            
            // Simulate preview loading
            setTimeout(() => {
                previewButton.innerHTML = originalText;
                previewButton.disabled = false;
                showNotification('Preview opened in a new tab', 'success');
            }, 1500);
        }
        
        // Notification system
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            // Add to DOM
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            // Remove after delay
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Initialize team members data
        let teamMembers = [
            {
                name: "John Doe",
                position: "Founder & Host",
                bio: "With over 15 years of experience in hospitality, John brings warmth and expertise to every guest interaction.",
                image: "https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
            },
            {
                name: "Sarah Smith",
                position: "Cultural Director",
                bio: "Sarah ensures that every guest experiences the authentic culture and traditions of Rwanda.",
                image: "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
            },
            {
                name: "Michael Johnson",
                position: "Operations Manager",
                bio: "Michael oversees the smooth running of our homestay, ensuring every detail is perfect.",
                image: "https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
            }
        ];

        // Function to render team cards
        function renderTeamCards() {
            const teamCardsContainer = document.getElementById('teamCards');
            teamCardsContainer.innerHTML = '';

            teamMembers.forEach((member, index) => {
                const card = document.createElement('div');
                card.className = 'team-card';
                card.innerHTML = `
                    <div class="team-card-image">
                        <img src="${member.image}" alt="${member.name}">
                    </div>
                    <div class="team-card-content">
                        <h3>${member.name}</h3>
                        <p class="position">${member.position}</p>
                        <p class="bio">${member.bio}</p>
                    </div>
                    <div class="team-card-actions">
                        <button onclick="editTeamMember(${index})" class="btn-edit" data-tooltip="Edit Team Member">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteTeamMember(${index})" class="btn-delete" data-tooltip="Delete Team Member">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                teamCardsContainer.appendChild(card);
            });
        }

        // Modal functions with animations
        function openTeamModal() {
            const modal = document.getElementById('teamModal');
            modal.style.display = 'flex';
            
            // Add animation
            setTimeout(() => {
                modal.classList.add('show');
                document.getElementById('memberName').focus();
            }, 10);
        }

        function closeTeamModal() {
            const modal = document.getElementById('teamModal');
            modal.classList.remove('show');
            
            // Wait for animation to complete before hiding
            setTimeout(() => {
                modal.style.display = 'none';
                document.getElementById('teamMemberForm').reset();
                document.getElementById('memberImagePreviewContainer').style.display = 'none';
            }, 300);
        }

        // Team member form submission with loading state
        document.getElementById('teamMemberForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const saveButton = this.querySelector('button[type="submit"]');
            const originalText = saveButton.innerHTML;
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveButton.disabled = true;
            
            // Get form data
            const newMember = {
                name: document.getElementById('memberName').value,
                position: document.getElementById('memberPosition').value,
                bio: document.getElementById('memberBio').value,
                image: document.getElementById('memberImage').value
            };
            
            // Simulate saving delay
            setTimeout(() => {
                saveButton.innerHTML = '<i class="fas fa-check"></i> Saved!';
                saveButton.classList.add('success-state');
                
                // Add the new member and update UI
                teamMembers.push(newMember);
                renderTeamCards();
                
                // Show success notification
                showNotification('Team member added successfully!', 'success');
                
                // Reset button and close modal after delay
                setTimeout(() => {
                    saveButton.innerHTML = originalText;
                    saveButton.disabled = false;
                    saveButton.classList.remove('success-state');
                    closeTeamModal();
                }, 1000);
            }, 1500);
        });

        // Edit team member
        function editTeamMember(index) {
            const member = teamMembers[index];
            document.getElementById('memberName').value = member.name;
            document.getElementById('memberPosition').value = member.position;
            document.getElementById('memberBio').value = member.bio;
            document.getElementById('memberImage').value = member.image;
            openTeamModal();
            // Remove the old member when saving
            teamMembers.splice(index, 1);
        }

        // Delete team member with confirmation and notification
        function deleteTeamMember(index) {
            // Create a custom confirmation dialog instead of using browser confirm
            const confirmDialog = document.createElement('div');
            confirmDialog.className = 'notification';
            confirmDialog.innerHTML = `
                <div class="notification-content">
                    <i class="fas fa-question-circle"></i>
                    <span>Are you sure you want to delete this team member?</span>
                </div>
                <div style="display: flex; padding: 0 20px 15px; justify-content: flex-end; gap: 10px;">
                    <button class="btn-secondary" style="padding: 5px 10px; font-size: 0.85rem;">Cancel</button>
                    <button class="btn-primary" style="padding: 5px 10px; font-size: 0.85rem; background: var(--danger-color);">Delete</button>
                </div>
            `;
            
            // Add to DOM
            document.body.appendChild(confirmDialog);
            
            // Animate in
            setTimeout(() => {
                confirmDialog.classList.add('show');
            }, 10);
            
            // Handle button clicks
            const buttons = confirmDialog.querySelectorAll('button');
            buttons[0].addEventListener('click', () => {
                // Cancel
                confirmDialog.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(confirmDialog);
                }, 300);
            });
            
            buttons[1].addEventListener('click', () => {
                // Delete
                confirmDialog.classList.remove('show');
                
                setTimeout(() => {
                    document.body.removeChild(confirmDialog);
                    
                    // Remove the team member
                    teamMembers.splice(index, 1);
                    renderTeamCards();
                    
                    // Show success notification
                    showNotification('Team member deleted successfully', 'success');
                }, 300);
            });
        }

        // Initialize team cards on page load
        document.addEventListener('DOMContentLoaded', renderTeamCards);

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('teamModal');
            if (event.target === modal) {
                closeTeamModal();
            }
        }
    </script>
</body>
</html>
