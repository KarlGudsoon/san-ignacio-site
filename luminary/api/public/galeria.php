<?php
header("Content-Type: application/json; charset=utf-8");

$base      = realpath(__DIR__ . "/../../../assets/img/gallery");
$categoria = $_GET["categoria"] ?? "";
$pagina    = max(1, (int)($_GET["pagina"] ?? 1));
$porPagina = 48;

// Sin categoría: lista de carpetas (L2025, L2026...)
if ($categoria === "") {
    $dirs = array_map('basename', glob("$base/*", GLOB_ONLYDIR));
    natsort($dirs);
    echo json_encode(array_values(array_reverse($dirs)));
    exit;
}

if (!preg_match('/^[A-Za-z0-9_-]+$/', $categoria) || !is_dir("$base/$categoria")) {
    http_response_code(400);
    echo json_encode(["error" => "Categoría inválida"]);
    exit;
}

// Lista las imágenes (scandir en vez de GLOB_BRACE, que no existe en todos los servidores)
$archivos = [];
foreach (scandir("$base/$categoria") as $f) {
    if (preg_match('/\.(jpe?g|png|webp)$/i', $f)) {
        $archivos[] = $f;
    }
}
natsort($archivos);
$archivos = array_values($archivos);

$total   = count($archivos);
$paginas = max(1, (int)ceil($total / $porPagina));
$pagina  = min($pagina, $paginas);

$slice = array_slice($archivos, ($pagina - 1) * $porPagina, $porPagina);
$urls  = array_map(
    fn($f) => "/assets/img/gallery/$categoria/" . rawurlencode($f),
    $slice
);

echo json_encode([
    "total"   => $total,
    "pagina"  => $pagina,
    "paginas" => $paginas,
    "fotos"   => $urls,
]);