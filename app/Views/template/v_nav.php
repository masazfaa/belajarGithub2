<body>

      <!-- Navigasi Panel -->
  <div class="nav-panel" id="navPanel">
    <div class="close-btn">
      <button id="closeNav" title="Close Navigation">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <ul>
      <li><a href="<?= base_url(); ?>">Home</a></li>
      <li><a href="<?= base_url('home/data') ?>">Data Management</a></li>
      <li><a href="<?= base_url('home/logout') ?>">Logout</a></li>
    </ul>
  </div>