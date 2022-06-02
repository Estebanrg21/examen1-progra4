  <!--   Core JS File   -->
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/soft-ui-dashboard.js"></script>
  <?php if (isset($popSuccessModal) || isset($popErrorModal) || isset($popDangerModal)) : ?>
    <script type="text/javascript">
      let modal = new bootstrap.Modal(document.getElementById('<?php echo $classModal ?>Modal'));
      modal.show();
    </script>
  <?php endif; ?>