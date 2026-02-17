{*
============================================
GWJ PDF VIEWER TEMPLATE
Archivo: display.tpl
============================================
*}

<!DOCTYPE html>
<html lang="{$currentLocale|replace:"_":"-"}">

<head>
    <meta charset="{$defaultCharset|escape}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{$title|escape}</title>

    <link rel="stylesheet" href="{$pluginUrl}/templates/display.css">

    {load_header context="frontend"}
</head>

<body class="gwj-body">

    <header class="gwj-header">

        <div class="gwj-title">
            {$title|escape}
        </div>

        <div class="gwj-actions">
            <a href="{$parentUrl}" class="gwj-btn gwj-btn-secondary">
                ← Volver
            </a>

            <a href="{$pdfUrl}" class="gwj-btn" download>
                ⬇ Descargar
            </a>
        </div>

    </header>

    <div class="gwj-pdf-container">
        <iframe id="gwjPdfFrame" allow="fullscreen"></iframe>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            var baseViewer = "{$pluginUrl}/pdfjs/web/viewer.html?file=";
            var pdfUrl = {$pdfUrl|json_encode:JSON_UNESCAPED_SLASHES};

            // 🔥 Zoom 70% + Sidebar thumbnails abierta
            var fullUrl = baseViewer +
                encodeURIComponent(pdfUrl) +
                "#zoom=70&pagemode=thumbs";

            document.getElementById("gwjPdfFrame").src = fullUrl;
        });
    </script>

</body>
</html>
