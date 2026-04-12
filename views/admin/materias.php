<?php
/**
 * Vista: registro y listado de asignaturas.
 * Variables: $mensaje, $materias, $editar, $ef, $hidDocente, $modValForm, $diaValForm, $idProgForm,
 *            $docBuscarDoc, $docBuscarNom, $docentesLookupJson
 */
?>
<main class="container pb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4" style="color:#0d47a1;">Catálogo de asignaturas</h1>
    <a class="btn btn-outline-secondary btn-sm" href="<?= h(url('admin/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <?php if ($mensaje): ?>
    <div class="alert <?= (strpos($mensaje, 'No se puede') !== false || strpos($mensaje, 'debe indicar') !== false || strpos($mensaje, 'Seleccione') !== false || strpos($mensaje, 'Busque') !== false || strpos($mensaje, 'Indique') !== false || strpos($mensaje, 'misma carrera') !== false || strpos($mensaje, 'Docente no encontrado') !== false || strpos($mensaje, 'sede del docente') !== false) ? 'alert-warning' : 'alert-success' ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <form method="post" id="form-materia" class="mb-4">
    <input type="hidden" name="accion" value="guardar">
    <?php if ($editar): ?>
      <input type="hidden" name="id_materia" value="<?= (int) $editar['id_materia'] ?>">
    <?php endif; ?>
    <input type="hidden" name="id_docente" id="hid-id-docente" value="<?= $hidDocente ?>">

    <div class="card shadow-sm mb-3">
      <div class="card-body">
        <h2 class="form-section-title h6"><?= $editar ? 'Editar asignatura' : 'Registrar asignatura' ?></h2>
        <p class="small text-muted mb-3">Datos generales del curso y la carrera a la que pertenece.</p>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Código</label>
            <input type="text" name="codigo" class="form-control" required value="<?= h($ef['codigo'] ?? '') ?>">
          </div>
          <div class="col-md-8">
            <label class="form-label">Nombre de la asignatura</label>
            <input type="text" name="nombre" class="form-control" required value="<?= h($ef['nombre'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Créditos</label>
            <input type="number" name="creditos" class="form-control" min="1" max="30" required value="<?= h((string) ($ef['creditos'] ?? '3')) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Semestre de la asignatura</label>
            <input type="number" name="semestre" class="form-control" min="1" max="20" required value="<?= h((string) ($ef['semestre'] ?? '1')) ?>">
          </div>
          <div class="col-md-8">
            <label class="form-label">Carrera / programa</label>
            <select name="id_programa" id="fld-id-programa" class="form-select" required>
              <option value="">Seleccione la carrera...</option>
              <?php foreach (diccionario_programas() as $p): ?>
                <option value="<?= (int) $p['id'] ?>" data-sede="<?= (int) ($p['id_sede'] ?? 1) ?>" <?= $idProgForm === (int) $p['id'] ? 'selected' : '' ?>>
                  <?= h('[' . $p['codigo'] . '] ' . $p['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm mb-3">
      <div class="card-body">
        <h2 class="form-section-title h6">Datos académicos</h2>
        <p class="small text-muted mb-3">Horario de la clase y modalidad.</p>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Día</label>
            <select name="dia_clase" class="form-select" required>
              <option value="">Seleccione...</option>
              <?php foreach (materia_dias_clase_opciones() as $cod => $etq): ?>
                <option value="<?= h($cod) ?>" <?= $diaValForm === $cod ? 'selected' : '' ?>><?= h($etq) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Hora inicio</label>
            <input type="time" name="hora_inicio" class="form-control" required value="<?= h((string) ($ef['hora_inicio'] ?? '')) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Hora fin</label>
            <input type="time" name="hora_fin" class="form-control" required value="<?= h((string) ($ef['hora_fin'] ?? '')) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Modalidad</label>
            <select name="modalidad" id="fld-modalidad" class="form-select" required>
              <option value="">Seleccione...</option>
              <option value="virtual" <?= $modValForm === 'virtual' ? 'selected' : '' ?>>Virtual</option>
              <option value="presencial" <?= $modValForm === 'presencial' ? 'selected' : '' ?>>Presencial</option>
            </select>
          </div>
          <div class="col-md-8" id="salon-wrap">
            <label class="form-label">Salón / aula</label>
            <input type="text" name="salon" id="fld-salon" class="form-control" maxlength="80"
              placeholder="Ej. A-301, Lab. 2 (solo presencial)"
              value="<?= h((string) ($ef['salon'] ?? '')) ?>">
            <div class="form-text">Obligatorio si la modalidad es presencial.</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm mb-3">
      <div class="card-body">
        <h2 class="form-section-title h6">Docente</h2>
        <p class="small text-muted mb-3">Busque por número de documento al docente registrado para la <strong>misma carrera</strong> y sede que eligió arriba (el diccionario asocia cada programa a Cúcuta u Ocaña).</p>
        <div class="row g-3 align-items-end">
          <div class="col-md-5">
            <label class="form-label">Identificación del docente</label>
            <input type="text" id="inp-doc-docente" class="form-control" autocomplete="off"
              placeholder="Ingrese el número de identificación" value="<?= h($docBuscarDoc) ?>">
          </div>
          <div class="col-md-3">
            <button type="button" class="btn btn-outline-primary w-100" id="btn-buscar-docente">Buscar</button>
          </div>
          <div class="col-md-4">
            <label class="form-label">Nombre del docente</label>
            <input type="text" id="txt-nombre-docente" class="form-control bg-light" readonly
              placeholder="Se carga al validar la identificación" value="<?= h($docBuscarNom) ?>">
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex flex-wrap gap-2">
      <button type="submit" class="btn btn-primary"><?= $editar ? 'Actualizar asignatura' : 'Registrar asignatura' ?></button>
      <a class="btn btn-outline-secondary" href="<?= h(url('admin/dashboard.php')) ?>">Volver</a>
      <?php if ($editar): ?>
        <a class="btn btn-outline-secondary" href="<?= h(url('admin/materias.php')) ?>">Cancelar edición</a>
      <?php endif; ?>
    </div>
  </form>

  <h2 class="h6 form-section-title">Listado</h2>
  <div class="table-responsive card shadow-sm">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Código</th>
          <th>Asignatura</th>
          <th>Carrera</th>
          <th>Horario</th>
          <th>Créd.</th>
          <th>Sem.</th>
          <th>Modalidad</th>
          <th>Salón</th>
          <th>Docente</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($materias as $m): ?>
          <tr>
            <td><?= (int) $m['id_materia'] ?></td>
            <td><?= h($m['codigo'] ?? '') ?></td>
            <td><?= h($m['nombre'] ?? '') ?></td>
            <td class="small"><?= h(materia_programa_label($m)) ?></td>
            <td class="small text-nowrap"><?= h(materia_horario_resumen($m)) ?></td>
            <td><?= (int) ($m['creditos'] ?? 0) ?></td>
            <td><?= (int) ($m['semestre'] ?? 0) ?></td>
            <td><?= h(materia_modalidad_etiqueta($m)) ?></td>
            <td><?= h((string) ($m['modalidad'] ?? '') === 'presencial' ? ($m['salon'] ?? '') : '—') ?></td>
            <td><?= h(docente_nombre((int) ($m['id_docente'] ?? 0))) ?></td>
            <td class="table-actions text-nowrap">
              <a class="btn btn-sm btn-outline-primary" href="<?= h(url('admin/materias.php?editar=' . (int) $m['id_materia'])) ?>">Editar</a>
              <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta asignatura?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_materia" value="<?= (int) $m['id_materia'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$materias): ?>
          <tr><td colspan="11" class="text-center text-muted py-4">No hay asignaturas. Registre docentes primero.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<script>window.DOCENTES_MATERIAS = <?= $docentesLookupJson ?>;</script>
<script src="<?= h(asset_url('js/admin-materias-form.js')) ?>"></script>
