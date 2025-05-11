(() => {
	const tagsInput = document.querySelector("#tags_input");

	// si el input existe
	if (tagsInput) {
		let tags = []; // arreglo para guardar los tags

		// Escuchar los cambios en el input
		tagsInput.addEventListener("keypress", guardarTag);

		function guardarTag(event) {
			if (event.keyCode === 44) {
				if (e.target.value.trim() === "" || e.target.value < 1) {
					return; // si el input está vacío o es menor a 1, no hacer nada
				}
				event.preventDefault(); // evitar el comportamiento por defecto de la tecla 

				// si la tecla es una coma
				tags = [...tags, event.target.value.trim()]; // agregar el valor del input al arreglo

				tagsInput.value = ""; // limpiar el input

				console.log(tags);
			}
		}
	}
})(); // IIFE
