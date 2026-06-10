    </main>
</div>

<div id="toast" class="fixed bottom-5 right-5 hidden bg-blue-600 text-white px-4 py-3 rounded-xl shadow-2xl z-50 flex items-center space-x-3">
    <span id="toast-message" class="text-sm font-medium"></span>
</div>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/categories.js"></script>
<?php if (isset($pageScript)): ?>
    <script src="/assets/js/<?= $pageScript ?>.js"></script>
<?php endif; ?>
<script>lucide.createIcons();</script>
</body>
</html>
