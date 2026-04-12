<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_role(ROLE_ADMIN);

$mensaje = '';
$tipoMsg = 'success';
$estudiantes = load_data('estudiantes');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = post('accion', '');
    if ($accion === 'eliminar') {
        $id = (int) post('id_estudiante', '0');
        $estudiantes = array_values(array_filter($estudiantes, static fn ($e) => (int) ($e['id_estudiante'] ?? 0) !== $id));
        save_data('estudiantes', $estudiantes);
        $mat = load_data('matriculas');
        $mat = array_values(array_filter($mat, static fn ($m) => (int) ($m['id_estudiante'] ?? 0) !== $id));
        save_data('matriculas', $mat);
        $mensaje = 'Estudiante eliminado.';
    } elseif ($accion === 'guardar') {
        $idProg = (int) post('id_programa', '0');
        $clavePost = post('clave', '');
        $row = [
            'nombre' => post('nombre', ''),
            'apellido' => post('apellido', ''),
            'documento' => post('documento', ''),
            'correo' => post('correo', ''),
            'telefono' => post('telefono', ''),
            'id_programa' => $idProg,
            'programa' => programa_label_by_id($idProg),
            'semestre' => (int) post('semestre', '1'),
            'id_sede' => (int) post('id_sede', '1'),
            'id_jornada' => (int) post('id_jornada', '1'),
        ];
        $editId = (int) post('id_estudiante', '0');
        if ($editId > 0) {
            foreach ($estudiantes as &$e) {
                if ((int) ($e['id_estudiante'] ?? 0) === $editId) {
                    if ($clavePost !== '') {
                        $e['clave'] = $clavePost;
                    }
                    $e = array_merge($e, $row);
                    break;
                }
            }
            unset($e);
            $mensaje = 'Estudiante actualizado.';
        } else {
            $row['id_estudiante'] = next_numeric_id($estudiantes, 'id_estudiante');
            $row['clave'] = $clavePost !== '' ? $clavePost : '123456';
            $estudiantes[] = $row;
            $mensaje = 'Estudiante registrado.' . ($clavePost === '' ? ' Contraseña por defecto: 123456.' : '');
        }
        save_data('estudiantes', $estudiantes);
        $tipoMsg = 'success';
    }
}

$estudiantes = load_data('estudiantes');
$editar = null;
$eid = (int) (get('editar') ?? '0');
if ($eid > 0) {
    $editar = repo_estudiante_por_id($eid);
}

$pageTitle = 'Registro de estudiantes';
require PARTIALS_PATH . '/header.php';
?>
<main class="container pb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4" style="color:#0d47a1;">Registro de estudiantes</h1>
    <a class="btn btn-outline-secondary btn-sm" href="<?= h(url('admin/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <?php if ($mensaje): ?>
    <div class="alert alert-<?= h($tipoMsg) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h2 class="form-section-title h6"><?= $editar ? 'Editar estudiante' : 'Nuevo estudiante' ?></h2>
      <form method="post" class="row g-3">
        <input type="hidden" name="accion" value="guardar">
        <?php if ($editar): ?>
          <input type="hidden" name="id_estudiante" value="<?= (int) $editar['id_estudiante'] ?>">
        <?php endif; ?>
        <div class="col-md-6">
          <label class="form-label">Nombres</label>
          <input type="text" name="nombre" class="form-control" required value="<?= h($editar['nombre'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Apellidos</label>
          <input type="text" name="apellido" class="form-control" required value="<?= h($editar['apellido'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Documento</label>
          <input type="text" name="documento" class="form-control" required value="<?= h($editar['documento'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Correo</label>
          <input type="email" name="correo" class="form-control" required value="<?= h($editar['correo'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Teléfono</label>
          <input type="text" name="telefono" class="form-control" value="<?= h($editar['telefono'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Programa académico</label>
          <select name="id_programa" class="form-select" required>
            <option value="">Seleccione...</option>
            <?php foreach (diccionario_programas() as $p): ?>
              <option value="<?= (int) $p['id'] ?>" <?= isset($editar['id_programa']) && (int) $editar['id_programa'] === (int) $p['id'] ? 'selected' : '' ?>>
                <?= h('[' . $p['codigo'] . '] ' . $p['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Semestre</label>
          <input type="number" name="semestre" class="form-control" min="1" max="20" required value="<?= h((string) ($editar['semestre'] ?? '1')) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Sede</label>
          <select name="id_sede" class="form-select" required>
            <?php foreach (diccionario_sedes() as $s): ?>
              <option value="<?= (int) $s['id'] ?>" <?= isset($editar['id_sede']) && (int) $editar['id_sede'] === (int) $s['id'] ? 'selected' : '' ?>><?= h($s['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Jornada</label>
          <select name="id_jornada" class="form-select" required>
            <?php foreach (diccionario_jornadas() as $j): ?>
              <option value="<?= (int) $j['id'] ?>" <?= isset($editar['id_jornada']) && (int) $editar['id_jornada'] === (int) $j['id'] ? 'selected' : '' ?>><?= h($j['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Contraseña de acceso <?= $editar ? '(dejar vacío para no cambiar)' : '' ?></label>
          <input type="password" name="clave" class="form-control" autocomplete="new-password" placeholder="<?= $editar ? 'Sin cambios' : 'Por defecto 123456' ?>">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary"><?= $editar ? 'Actualizar' : 'Registrar' ?></button>
          <?php if ($editar): ?>
            <a class="btn btn-secondary" href="<?= h(url('admin/estudiantes.php')) ?>">Cancelar edición</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <h2 class="h6 form-section-title">Listado</h2>
  <div class="table-responsive card shadow-sm">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Documento</th>
          <th>Nombre</th>
          <th>Programa</th>
          <th>Sem.</th>
          <th>Sede</th>
          <th>Jornada</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($estudiantes as $e): ?>
          <tr>
            <td><?= (int) $e['id_estudiante'] ?></td>
            <td><?= h($e['documento'] ?? '') ?></td>
            <td><?= h(trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? ''))) ?></td>
            <td class="small"><?= h($e['programa'] ?? '') ?></td>
            <td><?= (int) ($e['semestre'] ?? 0) ?></td>
            <td><?= h(sede_nombre(isset($e['id_sede']) ? (int) $e['id_sede'] : null)) ?></td>
            <td><?= h(jornada_nombre(isset($e['id_jornada']) ? (int) $e['id_jornada'] : null)) ?></td>
            <td class="table-actions text-nowrap">
              <a class="btn btn-sm btn-outline-primary" href="<?= h(url('admin/estudiantes.php?editar=' . (int) $e['id_estudiante'])) ?>">Editar</a>
              <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar este estudiante y sus matrículas?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_estudiante" value="<?= (int) $e['id_estudiante'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$estudiantes): ?>
          <tr><td colspan="8" class="text-center text-muted py-4">No hay estudiantes registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php require PARTIALS_PATH . '/footer.php'; ?>
