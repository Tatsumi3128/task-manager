</main>
<footer class="text-center text-muted small py-3">
    &copy; <?= date('Y') ?> TaskFlow &mdash; Web Application Development Final Project
    </p>

    <p class="mb-1">For more information, contact:</p>

    <p class="mb-1">
        Facebook:
        <a href="https://www.facebook.com/share/1K9EBFE6Zg/"
           target="_blank"
           rel="noopener noreferrer"
           class="text-decoration-none">
            Vincent Flores Navrro
        </a>
    </p>

    <p class="mb-0">
        Gmail:
        <a href="mailto:vnnavarro123@gmail.com"
           class="text-decoration-none">
            vnnavarro123@gmail.com
        </a>
    </p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>window.CSRF_TOKEN = <?= json_encode(csrf_token()) ?>;</script>
<script src="assets/js/app.js"></script>
</body>
</html>
