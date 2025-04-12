<?php
// Define una constante para indicar que es la página principal
define('LANDING_PAGE', true);

// Incluye el header especial para landing page
require_once 'landing-header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h2>Transformamos tu visión en software de clase mundial</h2>
        <p>Soluciones tecnológicas personalizadas que impulsan el crecimiento de tu negocio mediante innovación y experiencia técnica.</p>
        <a href="#contacto" class="cta-button">Consulta Gratis</a>
        
        <div class="hero-image">
            <img src="assets/images/redd-francisco-5U_28ojjgms-unsplash.jpg" alt="Equipo de desarrollo trabajando">
        </div>
    </div>
</section>

<!-- Services -->
<section class="services" id="services">
    <div class="container">
        <div class="section-title">
            <h3>Nuestros Servicios de Consultoría</h3>
            <p>Ofrecemos soluciones integrales adaptadas a las necesidades específicas de tu organización</p>
        </div>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <img src="assets/images/Ruler.png" alt="Desarrollo Custom">
                </div>
                <h4>Desarrollo a Medida</h4>
                <p>Software empresarial diseñado específicamente para tus procesos y necesidades operativas.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <img src="assets/images/Cloud Development.png" alt="Cloud Solutions">
                </div>
                <h4>Soluciones en la Nube</h4>
                <p>Migración, implementación y optimización de infraestructura cloud para mayor escalabilidad.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <img src="assets/images/Brain.png" alt="Inteligencia Artificial">
                </div>
                <h4>Inteligencia Artificial</h4>
                <p>Implementamos modelos de ML y AI para automatizar procesos y extraer insights de tus datos.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <img src="assets/images/Paint Brush.png" alt="Experiencia de Usuario">
                </div>
                <h4>Diseño UX/UI</h4>
                <p>Interfaces intuitivas y atractivas que mejoran la experiencia del usuario final.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <img src="assets/images/Lock.png" alt="Ciberseguridad">
                </div>
                <h4>Ciberseguridad</h4>
                <p>Protección de tus sistemas y datos con las últimas tecnologías de seguridad informática.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <img src="assets/images/Conflict.png" alt="Optimización">
                </div>
                <h4>Optimización de Sistemas</h4>
                <p>Mejoramos el rendimiento de tus aplicaciones existentes para mayor eficiencia.</p>
            </div>
        </div>
    </div>
</section>

<!-- Clients -->
<section class="clients" id="nosotros">
    <div class="container">
        <div class="section-title">
            <h3>Confían en Nosotros</h3>
            <p>Hemos colaborado con empresas líderes en diversos sectores</p>
        </div>
        
        <div class="client-logos">
            <img src="assets/images/Nike.png" alt="Logo Empresa 1">
            <img src="assets/images/Samsung.png" alt="Logo Empresa 2">
            <img src="assets/images/Citibank Squared.png" alt="Logo Empresa 3">
            <img src="assets/images/Apple Inc.png" alt="Logo Empresa 4">
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials" id="casos">
    <div class="container">
        <div class="section-title">
            <h3>Lo que dicen nuestros clientes</h3>
            <p>Testimonios de empresas que han transformado sus operaciones con nuestras soluciones</p>
        </div>
        
        <div class="testimonial-grid">
            <div class="testimonial-card">
                <div class="testimonial-text">
                    <p>"NexusTech implementó un sistema ERP que ha optimizado nuestros procesos logísticos en un 40%. El equipo demostró un profundo conocimiento técnico y gran capacidad para entender nuestras necesidades específicas."</p>
                </div>
                <div class="testimonial-author">
                    <img src="assets/images/User Male.png" alt="Juan Martínez">
                    <div class="author-info">
                        <h5>Juan Martínez</h5>
                        <p>Director de TI, RetailCorp</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-text">
                    <p>"La aplicación móvil desarrollada por NexusTech ha mejorado significativamente la experiencia de nuestros clientes. Han sido socios estratégicos desde el diseño inicial hasta el lanzamiento y soporte continuo."</p>
                </div>
                <div class="testimonial-author">
                    <img src="assets/images/Female User.png" alt="Ana Sánchez">
                    <div class="author-info">
                        <h5>Ana Sánchez</h5>
                        <p>Gerente de Producto, FinTech Solutions</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-text">
                    <p>"Gracias a la solución de análisis de datos implementada, ahora podemos tomar decisiones basadas en información en tiempo real. El equipo de NexusTech superó nuestras expectativas en cada fase del proyecto."</p>
                </div>
                <div class="testimonial-author">
                    <img src="assets/images/User Male.png" alt="Carlos Rodríguez">
                    <div class="author-info">
                        <h5>Carlos Rodríguez</h5>
                        <p>CEO, DataInsights</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section" id="contacto">
    <div class="container">
        <h3>¿Listo para transformar tu negocio con tecnología?</h3>
        <p>Agenda una consultoría gratuita con nuestros expertos y descubre cómo podemos ayudarte a alcanzar tus objetivos tecnológicos.</p>
        <a href="contacto.php" class="cta-button large">Contactar a un Consultor</a>
    </div>
</section>

<?php
// Incluye el footer especial para landing page
require_once 'landing-footer.php';
?>