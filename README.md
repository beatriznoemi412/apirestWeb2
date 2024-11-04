INTEGRANTES
 .Lucia Micaela Moreira 
 .Beatriz Noemí Muñoz


APi REST FULL
- Se obtienen todas las ventas
Endpoint: /venta
Método HTTP: GET
Descripción: Este endpoint permite obtener todas las ventas registradas.
Respuesta: Retorna un array de objetos que representan las ventas.
Ejemplo de respuesta:json
[
{
      "id_venta": 34,
      "inmueble": "Departamento en pleno centro Tandil",
      "fecha_venta": "2024-09-04",
      "precio": 228990,
      "id_vendedor": 3,
      "foto_url": "https://cdn.pixabay.com/photo/2014/09/04/05/54/construction-435302_1280.jpg"
    },
    {
      "id_venta": 36,
      "inmueble": " Excepcional cabaña en frente lago de Tandil, en plena sierra",
      "fecha_venta": "2024-10-08",
      "precio": 287000,
      "id_vendedor": 3,
      "foto_url": "https://cdn.pixabay.com/photo/2016/09/23/10/20/cottage-1689224_1280.jpg"
    },
]
- Se obtiene una venta por ID
Endpoint: /venta/:id_venta
Método HTTP: GET
Descripción: Obtiene la información de una venta específica a partir de su ID.
Parámetros:
id_venta (obligatorio): El ID de la venta que se quiere consultar.
Respuesta: Retorna un objeto con la información de la venta correspondiente.
Ejemplo de solicitud: /venta/1
{
  "id_venta": 34,
  "inmueble": "Departamento en pleno centro Tandil",
  "fecha_venta": "2024-09-04",
  "precio": 228990,
  "id_vendedor": 3,
  "foto_url": "https://cdn.pixabay.com/photo/2014/09/04/05/54/construction-435302_1280.jpg"
}


- POST
Para crear una venta, se debe enviar una solicitud POST con los datos en el cuerpo de la solicitud (JSON):

Endpoint: /venta
URL: https://localhost/web2-ApiRest/api/venta  Método: POST

Cuerpo de la Solicitud (JSON):

{
    "inmueble": "Apartamento en el centro",
    "date": "2024-10-31",
    "price": 150000,
    "id_vendedor": 3,
    "image": "https://example.com/image.jpg"
}
Posibles Respuestas:

201 Created: La venta fue creada exitosamente y devuelve el objeto de la venta.

{
    "id": 77,
    "inmueble": "Apartamento en el centro",
    "date": "2024-10-31",
    "price": 150000,
    "id_vendedor": 3,
    "image": "https://example.com/image.jpg"
}
400 Bad Request: Todos los campos son obligatorios o la URL de la imagen no es válida.
{
    "Todos los campos son obligatorios."
}
404 Not Found: No hay vendedores disponibles para asignar la venta.
{
    "No hay vendedores disponibles."
}
500 Internal Server Error: Error en la inserción de la venta.
{
    "Error al insertar tarea"
}
- Se edita una venta existente
Endpoint: /venta/:id_venta
Método HTTP: PUT
Descripción: Actualiza los detalles de una venta existente.
Parámetros:
id (obligatorio): El ID de la venta a editar.

Cuerpo de la solicitud: Debe incluir todos los campos obligatorios de la venta.

json, ej.:
{
  "inmueble": "Tipo de inmueble",
  "fecha_venta": "YYYY-MM-DD",
  "precio": 500000,
  "id_vendedor": 123,
  "url_foto": "http://ejemplo.com/imagen.jpg"
}
Validaciones:

Todos los campos en el cuerpo de la solicitud son obligatorios (inmueble, fecha_venta, precio, id_vendedor, url_foto).
Si algún campo está vacío, el servidor responderá con un error 400 indicando que todos los campos son obligatorios.
Si no se encuentra una venta con el id_venta especificado, el servidor devolverá un error 404.
Ejemplo de solicitud:

PUT /venta/1
json, ej.:
{ 
  "id_venta":81;
  "inmueble": "Apartamento",
  "fecha_venta": "2023-01-15",
  "precio": 200000,
  "id_vendedor": 101,
  "url_foto": "http://ejemplo.com/foto.jpg"
}
Posibles respuestas:

200 OK: La venta fue actualizada exitosamente.
{
  "Devuelve el objeto actualizado"
}
400 Bad Request: Algún campo en el cuerpo de la solicitud está vacío.
{
  "Todos los campos son obligatorios."
}
404 Not Found: No se encontró una venta con el id_venta especificado.
json

{
  "Venta no encontrada."
}
500 Internal Server Error: Error al actualizar la venta.
json
{
  "Hubo un problema al actualizar la venta."
}

- Filtros y Ordenamiento de Ventas en SaleApiController
La API permite a los usuarios obtener una lista de ventas con distintos filtros y un orden personalizado. A continuación, se describen los parámetros de filtro y orden que se pueden usar, junto con ejemplos de cómo hacer estas solicitudes.

Filtros Disponibles:
1)Filtrar por Precio Mínimo y Máximo (min_price y max_price)

Permite obtener ventas cuyo precio esté dentro de un rango específico.
Ejemplo:
GET https://localhost/web2-ApiRest/api/venta?min_price=100000&max_price=500000
Descripción: Este endpoint devolverá todas las ventas cuyo precio esté entre 100000 y 500000 U$S.

2)Filtra las ventas según el ID del vendedor.
Ejemplo:
GET https://localhost/web2-ApiRest/api/venta?id_vendedor=3
Descripción: Este endpoint devolverá todas las ventas realizadas por el vendedor con ID 3.

3)Filtrar por Rango de Fechas (start_date y end_date)
Permite obtener ventas dentro de un rango de fechas específico.
Ejemplo:
GET https://localhost/web2-ApiRest/api/venta?start_date=2023-01-01&end_date=2023-12-31
Descripción: Este endpoint devolverá todas las ventas registradas entre el 1 de enero de 2023 y el 31 de diciembre de 2023.

4)Ordenamiento
El ordenamiento se realiza sobre los campos precio, id_vendedor y fecha, permitiendo al usuario especificar si desea ordenar en orden ascendente (asc) o descendente (desc).

Parámetro de Orden (sortOrder):
asc: Orden ascendente (de menor a mayor).
desc: Orden descendente (de mayor a menor).
Parámetro de Campo de Orden (sortField):
precio: Ordenar por precio.
id_vendedor: Ordenar por ID del vendedor.
fecha: Ordenar por fecha de venta.
Ejemplos de Endpoints
Orden Ascendente por Precio:

GET https://localhost/web2-ApiRest/api/venta?sortField=precio&sortOrder=asc

GET https://localhost/web2-ApiRest/api/venta?sortField=precio&sortOrder=desc

Orden Ascendente por ID del Vendedor:

GET https://localhost/web2-ApiRest/api/venta?sortField=id_vendedor&sortOrder=asc

GET https://localhost/web2-ApiRest/api/venta?sortField=id_vendedor&sortOrder=desc

Orden Ascendente por Fecha:

GET https://localhost/web2-ApiRest/api/venta?sortField=fecha&sortOrder=asc

GET https://localhost/web2-ApiRest/api/venta?sortField=fecha&sortOrder=desc
Orden descendente por fecha.

La API permite combinar estos filtros y el orden en una sola solicitud. Por ejemplo, para obtener ventas con un precio entre 100,000 y 500,000, realizadas por el vendedor con ID 3, en el año 2023, ordenadas de menor a mayor por precio, se puede utilizar:

GET https://localhost/web2-ApiRest/api/venta?min_price=100000&max_price=500000&id_vendedor=3&start_date=2023-01-01&end_date=2024-12-31&sortField=precio&sortOrder=asc

Estos filtros y opciones de orden permiten a los usuarios personalizar los resultados de acuerdo a sus necesidades, facilitando la consulta de datos específicos de ventas.

- PAGINACION
Para solicitar una lista paginada de ventas, puedes realizar una solicitud GET al endpoint correspondiente y agregar los parámetros limit y page a la URL

GET http://localhost:8000/api/ventas?limit=10&page=2
limit=10: La respuesta incluirá un máximo de 10 registros.
page=2: Se devolverán los registros correspondientes a la segunda página.
Cuando se utilizan parámetros de paginación como limit y offset, el proceso es el siguiente:

limit: Define cuántos registros se deben devolver en una sola respuesta.
offset: Indica cuántos registros se deben saltar antes de comenzar a devolver los resultados.
Cálculo del Offset
El offset se calcula automáticamente utilizando la siguiente fórmula:

$offset = ($page - 1) * $limit;
Por ejemplo:

Si page=2 y limit=10, el offset será 10, lo que significa que se omitirán los primeros 10 registros y se comenzará a recuperar desde el 11º registro.









