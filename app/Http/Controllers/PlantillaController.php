<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class PlantillaController extends Controller
{
    public function descargarPlantillaVotantes()
    {
        // Crear contenido XML para Excel (formato SpreadsheetML)
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Worksheet ss:Name="Plantilla Votantes">
  <Table>
   <Row>
    <Cell><Data ss:Type="String">ci</Data></Cell>
    <Cell><Data ss:Type="String">nombres</Data></Cell>
    <Cell><Data ss:Type="String">apellidos</Data></Cell>
    <Cell><Data ss:Type="String">telefono</Data></Cell>
    <Cell><Data ss:Type="String">email</Data></Cell>
    <Cell><Data ss:Type="String">direccion</Data></Cell>
    <Cell><Data ss:Type="String">barrio</Data></Cell>
    <Cell><Data ss:Type="String">zona</Data></Cell>
    <Cell><Data ss:Type="String">distrito</Data></Cell>
    <Cell><Data ss:Type="String">codigo_intencion</Data></Cell>
    <Cell><Data ss:Type="String">necesita_transporte</Data></Cell>
    <Cell><Data ss:Type="String">latitud</Data></Cell>
    <Cell><Data ss:Type="String">longitud</Data></Cell>
    <Cell><Data ss:Type="String">notas</Data></Cell>
   </Row>
   <Row>
    <Cell><Data ss:Type="String">1234567</Data></Cell>
    <Cell><Data ss:Type="String">Juan</Data></Cell>
    <Cell><Data ss:Type="String">Pérez García</Data></Cell>
    <Cell><Data ss:Type="String">0981-123456</Data></Cell>
    <Cell><Data ss:Type="String">juan@email.com</Data></Cell>
    <Cell><Data ss:Type="String">Av. Principal 123</Data></Cell>
    <Cell><Data ss:Type="String">Centro</Data></Cell>
    <Cell><Data ss:Type="String">Zona 1</Data></Cell>
    <Cell><Data ss:Type="String">Distrito 1</Data></Cell>
    <Cell><Data ss:Type="String">A</Data></Cell>
    <Cell><Data ss:Type="String">Si</Data></Cell>
    <Cell><Data ss:Type="String">-25.2867</Data></Cell>
    <Cell><Data ss:Type="String">-57.6333</Data></Cell>
    <Cell><Data ss:Type="String">Ejemplo</Data></Cell>
   </Row>
   <Row>
    <Cell><Data ss:Type="String">7654321</Data></Cell>
    <Cell><Data ss:Type="String">María</Data></Cell>
    <Cell><Data ss:Type="String">González López</Data></Cell>
    <Cell><Data ss:Type="String">0982-654321</Data></Cell>
    <Cell><Data ss:Type="String">maria@email.com</Data></Cell>
    <Cell><Data ss:Type="String">Calle Secundaria 456</Data></Cell>
    <Cell><Data ss:Type="String">San Blas</Data></Cell>
    <Cell><Data ss:Type="String">Zona 2</Data></Cell>
    <Cell><Data ss:Type="String">Distrito 2</Data></Cell>
    <Cell><Data ss:Type="String">B</Data></Cell>
    <Cell><Data ss:Type="String">No</Data></Cell>
    <Cell><Data ss:Type="String">-25.2900</Data></Cell>
    <Cell><Data ss:Type="String">-57.6400</Data></Cell>
    <Cell><Data ss:Type="String">Ejemplo</Data></Cell>
   </Row>
  </Table>
 </Worksheet>
</Workbook>';

        return response($xml, 200)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="plantilla_votantes.xls"')
            ->header('Cache-Control', 'max-age=0');
    }
}
