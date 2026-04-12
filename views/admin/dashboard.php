<?php
declare(strict_types=1);
/** Vista: panel principal del administrador. */
?>
<main class="container pb-5">
  <section class="hero-welcome">
    <h1>Bienvenido al Sistema Académico</h1>
    <p class="mb-0 text-secondary">Gestiona estudiantes, asignaturas y matrículas desde este panel central.</p>
  </section>

  <div id="carouselMain" class="carousel slide mb-4 rounded overflow-hidden shadow" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1200&q=80" class="d-block w-100" alt="" style="max-height:280px;object-fit:cover;">
        <div class="carousel-caption carousel-caption-overlay d-none d-md-block">
          <h2 class="h4 mb-1">Gestión integral académica</h2>
          <p class="small mb-0">Todo lo que necesitas para administrar tu institución</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=1200&q=80" class="d-block w-100" alt="" style="max-height:280px;object-fit:cover;">
        <div class="carousel-caption carousel-caption-overlay d-none d-md-block">
          <h2 class="h4 mb-1">Estudiantes y matrículas</h2>
          <p class="small mb-0">Registro alineado al diccionario de datos</p>
        </div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselMain" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselMain" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
  </div>

  <h2 class="h5 mb-3">Accesos rápidos</h2>
  <div class="row g-3">
    <div class="col-md-4 col-lg">
      <a href="<?= h(url('admin/estudiantes.php')) ?>" class="text-decoration-none text-dark">
        <div class="card quick-card p-3 text-center">
          <div class="icon-wrap text-primary"><i class="bi bi-mortarboard-fill"></i></div>
          <strong>Estudiantes</strong>
          <small class="text-muted">Registro y edición</small>
        </div>
      </a>
    </div>
    <div class="col-md-4 col-lg">
      <a href="<?= h(url('admin/docentes.php')) ?>" class="text-decoration-none text-dark">
        <div class="card quick-card p-3 text-center">
          <div class="icon-wrap text-success"><i class="bi bi-person-badge"></i></div>
          <strong>Docentes</strong>
          <small class="text-muted">Gestión de docentes</small>
        </div>
      </a>
    </div>
    <div class="col-md-4 col-lg">
      <a href="<?= h(url('admin/materias.php')) ?>" class="text-decoration-none text-dark">
        <div class="card quick-card p-3 text-center">
          <div class="icon-wrap text-warning"><i class="bi bi-book"></i></div>
          <strong>Asignaturas</strong>
          <small class="text-muted">Catálogo y docente asignado</small>
        </div>
      </a>
    </div>
    <div class="col-md-4 col-lg">
      <a href="<?= h(url('admin/matricular.php')) ?>" class="text-decoration-none text-dark">
        <div class="card quick-card p-3 text-center">
          <div class="icon-wrap text-info"><i class="bi bi-pencil-square"></i></div>
          <strong>Matricular</strong>
          <small class="text-muted">Inscripción de cursos</small>
        </div>
      </a>
    </div>
    <div class="col-md-4 col-lg">
      <a href="<?= h(url('admin/reportes.php')) ?>" class="text-decoration-none text-dark">
        <div class="card quick-card p-3 text-center">
          <div class="icon-wrap text-secondary"><i class="bi bi-table"></i></div>
          <strong>Reportes</strong>
          <small class="text-muted">Listados y acciones</small>
        </div>
      </a>
    </div>
  </div>
</main>
