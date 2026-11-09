        </main>
    </div>

<footer class="admin-footer">
    <div class="footer-content">
        <p class="copyright">&copy; <?= date('Y') ?> Brain Gym Academy. All rights reserved.</p>
        <div class="footer-links">
            <a href="/privacy"><i class="fas fa-shield-alt"></i> Privacy Policy</a>
            <a href="/terms"><i class="fas fa-file-contract"></i> Terms of Service</a>
            <a href="/support"><i class="fas fa-headset"></i> Support</a>
        </div>
    </div>
</footer>

<style>
.admin-footer {
    grid-column: 1 / -1;
    background: #ffffff;
    padding: 1.2rem 2rem;
    border-top: 1px solid #eee;
    box-shadow: 0 -2px 15px rgba(0,0,0,0.03);
    z-index: 100;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.copyright {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
}

.footer-links {
    display: flex;
    gap: 1.8rem;
}

.footer-links a {
    color: #444;
    text-decoration: none;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.footer-links a:hover {
    color: #d4145a;
    transform: translateY(-1px);
}

.footer-links i {
    font-size: 0.95rem;
}
</style>
</body>
</html>