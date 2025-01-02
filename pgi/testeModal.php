<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Modal com Cropper.js</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Cropper.js CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <style>
        .modal-backdrop {
            z-index: 1040 !important;
        }
        .modal-dialog {
            z-index: 1050 !important;
        }
        .cropper-container {
            z-index: 1060 !important;
        }
    </style>
</head>
<body>
    <!-- Botão para abrir o modal -->
    <button id="openModal" class="btn btn-primary">Abrir Modal</button>

    <!-- Modal -->
    <div class="modal fade" id="imageAdjustmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajustar Imagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="previewImage" style="max-width: 100%; max-height: 500px;">
                    <div id="successMessage" class="alert alert-success mt-3" style="display: none;">
                        Imagem salva com sucesso!
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="saveCrop" class="btn btn-success">Salvar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Cropper.js JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <!-- Script -->
    <script>
        // Configuração do Modal e Cropper
        document.getElementById('openModal').addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('imageAdjustmentModal'));
            modal.show();

            const image = document.getElementById('previewImage');
            image.src = 'https://via.placeholder.com/800'; // Imagem de exemplo

            const cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
            });

            document.getElementById('saveCrop').addEventListener('click', function () {
                const croppedCanvas = cropper.getCroppedCanvas();
                if (croppedCanvas) {
                    document.getElementById('successMessage').style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>
