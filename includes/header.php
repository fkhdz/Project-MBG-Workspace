<?php
/**
 * Komponen Header HTML (Tag <head> + style Tailwind)
 * Cukup dipanggil dengan: include 'includes/header.php'; 
 * Lalu set variabel $page_title sebelum include.
 *
 * Variabel opsional:
 *   $page_title    : judul halaman (default: 'MBG Workspace')
 *   $extra_head    : string tambahan di <head> (mis. CSS/JS tambahan)
 */
if (!isset($page_title))   $page_title = 'MBG Workspace';
if (!isset($extra_head))   $extra_head = '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= htmlspecialchars($page_title) ?> - MBG Workspace</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">

    <!-- Tailwind Config (Desain Sistem MBG) -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50:  '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        surface: '#f8fafc',
                    },
                    boxShadow: {
                        'soft': '0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.03)',
                        'glow': '0 4px 14px -2px rgba(14, 165, 233, 0.25)',
                    },
                },
            },
        };
    </script>

    <!-- Default styling untuk icon -->
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            user-select: none;
        }
        body {
            font-feature-settings: "cv11", "ss01";
        }
    </style>

    <?= $extra_head ?>
</head>
<body class="font-sans bg-surface text-slate-800 antialiased selection:bg-primary-100 selection:text-primary-700">