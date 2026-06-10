</main>
</div><!-- /#body-layout -->
</div><!-- /#app-wrapper -->

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-5 right-5 hidden text-white px-4 py-3 z-50 flex items-center space-x-3"
     style="border-radius:12px;font-family:Inter,sans-serif;min-width:240px;">
    <span id="toast-message" class="text-sm font-medium"></span>
</div>

<!-- Modal -->
<div id="modal-container" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
    <div class="w-full max-w-md mx-4" style="background:var(--bg-card);border:1px solid var(--border-strong);border-radius:20px;padding:28px;box-shadow:var(--shadow-lg);animation:modalIn 0.2s cubic-bezier(0.34,1.56,0.64,1);">
        <div id="modal-body"></div>
    </div>
</div>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/categories.js"></script>
<?php if (isset($pageScript)): ?>
    <script src="/assets/js/<?= $pageScript ?>.js"></script>
<?php endif; ?>
<script>lucide.createIcons();</script>
</body>
</html>
