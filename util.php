<?php
require_once 'conexion.php';
function getPublishers(): array
{
    try {

        $con = getConnection();

        $stmt = $con->prepare("SELECT * FROM publishers ORDER BY publisher_id");

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    } finally {
        $con = null;
        $stmt = null;
    }
}



function createCheckbox(int $id): string
{
    return "<input type='checkbox' id='cb_{$id}' name='ids_seleccionados[]' value='{$id}' form='bulk-delete-form'>";
}
function showPublishers(array $data)
{
    if ($data) {
        echo "<table class='table'><tr> <th><input type='checkbox' name='cb_all'></th> <th>Id</th> 
        <th> Nombre </th> <th> <form id='bulk-delete-form' action='index.php' method='POST' > <button id='bulk-delete-button' type='submit' name='eliminar-seleccion' value='borrar' class='btn btn-danger' disabled>Eliminar selección</button> </form> </th> </tr> <tbody>";

        foreach ($data as $fila) {
            $id = htmlspecialchars($fila["publisher_id"], ENT_QUOTES | ENT_HTML5, "UTF-8");
            $nombre = htmlspecialchars($fila["name"], ENT_QUOTES | ENT_HTML5, "UTF-8");
            echo "<tr> <td> " . createCheckbox($id) . " </td> <td> {$id}</td> <td>{$nombre} </td>";

           

          

            echo '</tr>';
        }
        echo "</tbody></table>";
    } else {
        showMsg("No se encontraron registros", "primary");
    }
}

function showMsg(string $msg, string $claseCSS)
{
    echo "<div class=\"alert alert-$claseCSS\" role=\"alert\">
  $msg
</div>
";
}



function deletePublishers(array $ids): bool
{
    try {
        $con = getConnection();
        $con->beginTransaction();
        //Construimos la consulta con tantos placeholders como ids hay en el array
        $question_marks = [];
        foreach ($ids as $id) {
            $question_marks[] = '?';
        }
        $placeholders = implode(',', $question_marks);
        $stmt = $con->prepare("DELETE FROM publishers WHERE publisher_id IN ($placeholders)");
        // foreach ($ids as $index => $id) {
        //     $stmt->bindValue($index + 1, $id);
        // }
        $stmt->execute($ids);
        $eliminados = $stmt->rowCount();
        $con->commit();

        return $eliminados > 0;

    } catch (PDOException $e) {
        if (isset($con) && $con->inTransaction()) {
            $con->rollBack();
        }
        error_log("Ha ocurrido una excepción eliminando en publishers: " . $e->getMessage());
        throw $e;
    } finally {
        $con = null;
        $stmt = null;
    }
}
