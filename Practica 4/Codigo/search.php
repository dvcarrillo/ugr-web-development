<DOCTYPE html>
<html lang="es">

<head>
	<title>Busqueda</title>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	<script>
		function cleanList() {
			var listElement = document.getElementById('list');
			while (listElement.firstChild) {
				listElement.removeChild(listElement.firstChild);
			}
		}

		function actualizarLista(data, id) {
			var listElement = document.getElementById('list');
			var listItem = document.createElement('li');
			listItem.innerHTML = "<a href='index.php?option=show&item=" + i + "'>" + data.titulo + ": " + data.subtitulo + "</a>";
			listElement.appendChild(listItem);
		}

		$(document).ready(function () {
			$("#campoTexto").keyup(function () {
				var txt = $("input").val();
				txt = { "content": txt }

				$.ajax({
					data: txt,
					url: 'search_backend.php',
					type: 'post',
					beforeSend: function () {
						$("#status").html("Procesando, espere por favor...");
					},
					success: function (data, status) {
						if(status="succes"){
							$("#status").html("Busqueda procesada existosamente");
						}
						else{
							$("#status").html("Error: "+status);
						}
						resultsList = JSON.parse(data);
						cleanList()
						for (i = 0; i < resultsList.length; i++) {
							actualizarLista(resultsList[i], i)
						}
					}
				});
			});
		});
	</script>

</head>

<body>
	<input type="text" id="campoTexto" list="consejos" />

	<div id="results">
		<ul id="list">

		</ul>
	</div>

	<p id="status"></p>
</body>

</html>