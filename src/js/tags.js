(() => {
	const tagsInput = document.querySelector("#tags_input");

	// si el input existe
	if (tagsInput) {
		const tagsDiv = document.querySelector("#tags"); // seleccionar el div donde se mostrarán los tags
		const tagsInputHidden = document.querySelector('[name="tags"]'); // seleccionar el input hidden donde se guardarán los tags
		let tags = []; // arreglo para guardar los tags

		// Escuchar los cambios en el input
		tagsInput.addEventListener("keypress", guardarTag);

		function guardarTag(event) {
			if (event.keyCode === 44) {
				if (event.target.value.trim() === "" || event.target.value < 1) {
					return; // si el input está vacío o es menor a 1, no hacer nada
				}
				event.preventDefault(); // evitar el comportamiento por defecto de la tecla

				// si la tecla es una coma
				tags = [...tags, event.target.value.trim()]; // agregar el valor del input al arreglo
				tagsInput.value = ""; // limpiar el input

				console.log(tags);

				mostrarTags();
			}
		}

		/**
		 * Muestra los tags almacenados en el array `tags` dentro del elemento `tagsDiv`.
		 *
		 * Utiliza un bucle `for...of` para iterar sobre cada elemento del array `tags`.
		 * El bucle `for...of` permite recorrer directamente los valores del array,
		 * facilitando la creación de un elemento de lista (`LI`) para cada tag y su inserción en el DOM.
		 */
		function mostrarTags() {
			tagsDiv.textContent = ""; // limpiar el div

			for (const tag of tags) {
				const etiqueta = document.createElement("LI");
				etiqueta.classList.add("formulario__tag");
				etiqueta.textContent = tag; // agregar el texto del tag al elemento LI
				etiqueta.ondblclick = eliminarTag; // agregar el evento de doble clic para eliminar el tag
				tagsDiv.appendChild(etiqueta); // agregar el LI al div
			}

			actualizarInputHidden(); // actualizar el input hidden con los tags
		}

		function eliminarTag(event) {
			event.target.remove(); // eliminar el tag del DOM
			tags = tags.filter((tag) => tag !== event.target.textContent); // eliminar el tag del array
			actualizarInputHidden(); // actualizar el input hidden con los tags
		}

		function actualizarInputHidden() {
			tagsInputHidden.value = tags.toString();
		}
	}
})(); // IIFE
