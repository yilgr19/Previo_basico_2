<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_role(ROLE_ADMIN);

$mensaje = '';
$docentes = load_data('docentes');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = post('accion', '');
    if ($accion === 'eliminar') {
        $id = (int) post('id_docente', '0');
        $refs = load_data('materias');
        foreach ($refs as $m) {
            if ((int) ($m['id_docente'] ?? 0) === $id) {
                $mensaje = 'No se puede eliminar: hay asignaturas asignadas a este docente.';
                break;
            }
        }
        if ($mensaje === '') {
            $docentes = array_values(array_filter($docentes, static fn ($d) => (int) ($d['id_docente'] ?? 0) !== $id));
            save_data('docentes', $docentes);
            $mensaje = 'Docente eliminado.';
        }
    } elseif ($accion === 'guardar') {
        $row = [
            'nombre' => post('nombre', ''),
            'apellido' => post('apellido', ''),
            'documento' => post('documento', ''),
            'correo' => post('correo', ''),
            'telefono' => post('telefono', ''),
        ];
        $clavePost = post('clave', '');
        $editId = (int) post('id_docente', '0');
        if ($editId > 0) {
            foreach ($docentes as &$d) {
                if ((int) ($d['id_docente'] ?? 0) === $editId) {
                    if ($clavePost !== '') {
                        $d['clave'] = $clavePost;
                    }
                    $d = array_merge($d, $row);
                    break;
                }
            }
            unset($d);
            $mensaje = 'Docente actualizado.';
        } else {
            $row['id_docente'] = next_numeric_id($docentes, 'id_docente');
            $row['clave'] = $clavePost !== '' ? $clavePost : 'doc123';
            $docentes[] = $row;
            $mensaje = 'Docente registrado.' . ($clavePost === '' ? ' Contraseña por defecto: doc123.' : '');
        }
        save_data('docentes', $docentes);
    }
}

$docentes = load_data('docentes');
$editar = null;
$eid = (int) (get('editar') ?? '0');
if ($eid > 0) {
    $editar = repo_docente_por_id($eid);
}

$pageTitle = 'Registro de docentes';
require PARTIALS_PATH . '/header.php';
?>
<main class="container pb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4" style="color:#0d47a1;">Registro de docentes</h1>
    <a class="btn btn-outline-secondary btn-sm" href="<?= h(url('admin/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <?php if ($mensaje): ?>
    <div class="alert <?= (strpos($mensaje, 'No se puede') !== false) ? 'alert-warning' : 'alert-success' ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h2 class="form-section-title h6"><?= $editar ? 'Editar docente' : 'Nuevo docente' ?></h2>
      <form method="post" class="row g-3">
        <input type="hidden" name="accion" value="guardar">
        <?php if ($editar): ?>
          <input type="hidden" name="id_docente" value="<?= (int) $editar['id_docente'] ?>">
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
        <div class="col-12">
          <label class="form-label">Contraseña de acceso <?= $editar ? '(vacío = sin cambio)' : '' ?></label>
          <input type="password" name="clave" class="form-control" autocomplete="new-password" placeholder="<?= $editar ? 'Sin cambios' : 'Por defecto doc123' ?>">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary"><?= $editar ? 'Actualizar' : 'Registrar' ?></button>
          <?php if ($editar): ?>
            <a class="btn btn-secondary" href="<?= h(url('admin/docentes.php')) ?>">Cancelar edición</a>
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
          <th>Correo</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($docentes as $d): ?>
          <tr>
            <td><?= (int) $d['id_docente'] ?></td>
            <td><?= h($d['documento'] ?? '') ?></td>
            <td><?= h(trim(($d['nombre'] ?? '') . ' ' . ($d['apellido'] ?? ''))) ?></td>
            <td><?= h($d['correo'] ?? '') ?></td>
            <td class="table-actions text-nowrap">
              <a class="btn btn-sm btn-outline-primary" href="<?= h(url('admin/docentes.php?editar=' . (int) $d['id_docente'])) ?>">Editar</a>
              <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar este docente?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_docente" value="<?= (int) $d['id_docente'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$docentes): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">No hay docentes registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php require PARTIALS_PATH . '/footer.php'; ?>
