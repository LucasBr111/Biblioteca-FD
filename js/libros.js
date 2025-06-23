/*document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.books-grid').addEventListener('click', function (e) {
        // Buscar la tarjeta del libro más cercana, no el botón directamente
        const card = e.target.closest('.book-card');
        // Si no se hizo clic dentro de una tarjeta de libro, salir
        if (!card) {
            console.log("Clicked element is not inside a .book-card.");
            return;
        }
        // Asegúrate de que el clic NO fue en un botón específico que tenga otra función
        // Por ejemplo, si tuvieras un botón "Añadir a favoritos" dentro de la tarjeta
        // if (e.target.closest('.another-button-class')) {
        //     return; // No abrir el modal si se hizo clic en ese botón
        // }
        e.preventDefault(); // Previene la acción por defecto si el clic fue en un enlace/botón que la tuviera
        // Obtener datos del dataset de la tarjeta
        const bookData = {
            title: card.dataset.title,
            author: card.dataset.author,
            year: card.dataset.year,
            genre: card.dataset.genre,
            description: card.dataset.description,
            image: card.dataset.image,
            pdf: card.dataset.pdf,
            summary: card.dataset.summary
        };
        // Actualizar modal con los datos
        document.getElementById('modalBookTitle').textContent = bookData.title;
        document.getElementById('modalBookImage').src = bookData.image;
        document.getElementById('modalBookAuthor').textContent = bookData.author;
        document.getElementById('modalBookYear').textContent = bookData.year;
        document.getElementById('modalBookGenre').textContent = bookData.genre;
        document.getElementById('modalBookDescription').textContent = bookData.description;
        // Actualizar botones
        const modalDownloadButton = document.getElementById('modalDownloadButton');
        const modalResumeButton = document.getElementById('modalResumeButton');
        if (bookData.pdf && bookData.pdf !== 'assets/books/') {
            modalDownloadButton.href = bookData.pdf;
            modalDownloadButton.target = '_blank';
            modalDownloadButton.style.display = 'inline-block';
        } else {
            modalDownloadButton.style.display = 'none';
        }
        if (bookData.summary && bookData.summary !== 'null') {
            modalResumeButton.href = bookData.summary;
            modalResumeButton.target = '_blank';
            modalResumeButton.style.display = 'inline-block';
        } else {
            modalResumeButton.style.display = 'none';
        }
        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('bookDetailModal'));
        modal.show();
    });
});*/

document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.books-grid').addEventListener('click', function (e) {
        // Buscar la tarjeta del libro más cercana, no el botón directamente
        const card = e.target.closest('.book-card');
        // Si no se hizo clic dentro de una tarjeta de libro, salir
        if (!card) {
            console.log("Clicked element is not inside a .book-card.");
            return;
        }
        // Asegúrate de que el clic NO fue en un botón específico que tenga otra función
        // Por ejemplo, si tuvieras un botón "Añadir a favoritos" dentro de la tarjeta
        // if (e.target.closest('.another-button-class')) {
        //     return; // No abrir el modal si se hizo clic en ese botón
        // }
        e.preventDefault(); // Previene la acción por defecto si el clic fue en un enlace/botón que la tuviera
        // Obtener datos del dataset de la tarjeta
        const bookData = {
            id: card.dataset.id, // Asegúrate de obtener el ID
            title: card.dataset.title,
            author: card.dataset.author,
            year: card.dataset.year,
            genre: card.dataset.genre,
            description: card.dataset.description,
            image: card.dataset.image,
            pdf: card.dataset.pdf,
            summary: card.dataset.summary
        };
        // Actualizar modal con los datos
        const modalBookTitleElement = document.getElementById('modalBookTitle');
        modalBookTitleElement.textContent = bookData.title;
        modalBookTitleElement.dataset.id = bookData.id; // ¡NUEVO! Guardar el ID en el título del modal

        document.getElementById('modalBookImage').src = bookData.image;
        document.getElementById('modalBookAuthor').textContent = bookData.author;
        document.getElementById('modalBookYear').textContent = bookData.year;
        document.getElementById('modalBookGenre').textContent = bookData.genre;
        document.getElementById('modalBookDescription').textContent = bookData.description;
        // Actualizar botones
        const modalDownloadButton = document.getElementById('modalDownloadButton');
        const modalResumeButton = document.getElementById('modalResumeButton');
        const modalDeleteButton = document.getElementById('modalDeleteButton'); // Obtenemos también el botón de eliminar

        if (bookData.pdf && bookData.pdf !== 'assets/books/') {
            modalDownloadButton.href = bookData.pdf;
            modalDownloadButton.style.display = 'inline-block';
        } else {
            modalDownloadButton.style.display = 'none';
        }
        if (bookData.summary && bookData.summary !== 'null') {
            modalResumeButton.href = bookData.summary;
            modalResumeButton.style.display = 'inline-block';
        } else {
            modalResumeButton.style.display = 'none';
        }

        // Manejar la visibilidad del botón de eliminar (solo si existe y el usuario es admin)
        // La lógica PHP ya oculta/muestra el botón inicialmente, aquí lo activamos por si acaso
        // y para asegurar que la referencia al elemento es correcta.
        if (modalDeleteButton) {
            // No es necesario cambiar su display aquí si ya lo maneja PHP por la condición $userAdmin
            // Pero si por alguna razón el display está 'none' y PHP lo permite, lo mostramos.
            // Considera que el JS de main.php ya maneja la visibilidad basada en $userAdmin
            // Si el botón no debe mostrarse para no-admin, el if ($userAdmin) de PHP ya lo hará.
        }

        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('bookDetailModal'));
        modal.show();
    });
});
