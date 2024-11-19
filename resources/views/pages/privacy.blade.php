<x-layouts.app>
    <!-- Page Header Start -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>Privacy Policy</h2>
                </div>
                <div class="col-12">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="">Privacy Policy</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="privacy-content">
                    <section class="mb-5">
                        <h3>Cookie Policy</h3>
                        <p>We use cookies and similar technologies to help personalize content, tailor and measure ads, and provide a better experience. By clicking 'Accept' you agree to this use of cookies and data.</p>
                    </section>

                    <section class="mb-5">
                        <h3>Information We Collect</h3>
                        <ul>
                            <li>Basic visitor information (IP address, browser type, device info)</li>
                            <li>Information you voluntarily provide through forms</li>
                            <li>Donation and transaction details</li>
                            <li>Newsletter subscription information</li>
                        </ul>
                    </section>

                    <section class="mb-5">
                        <h3>How We Use Your Information</h3>
                        <ul>
                            <li>To provide and maintain our services</li>
                            <li>To notify you about changes to our services</li>
                            <li>To provide customer support</li>
                            <li>To gather analysis or valuable information to improve our services</li>
                            <li>To monitor the usage of our services</li>
                        </ul>
                    </section>

                    <section class="mb-5">
                        <h3>Data Protection</h3>
                        <p>We implement appropriate security measures to protect your personal information. However, no method of transmission over the internet is 100% secure.</p>
                    </section>

                    <section class="mb-5">
                        <h3>Third-Party Services</h3>
                        <p>We may employ third-party companies and individuals for:</p>
                        <ul>
                            <li>Payment processing</li>
                            <li>Analytics</li>
                            <li>Email marketing</li>
                            <li>Social media integration</li>
                        </ul>
                    </section>
                    <div class="section" id="data-deletion">
                        <h3>Your Data Deletion Rights</h3>
                        <div class="content">
                            <p>You have the right to request deletion of your personal data from our systems. Here's how:</p>
                            
                            <h4>Option 1: Self-Service Deletion</h4>
                            <ol>
                                <li>Log into your account</li>
                                <li>Navigate to Profile Settings</li>
                                <li>Click on "Delete My Account"</li>
                                <li>Confirm your decision</li>
                            </ol>

                            <h4>Option 2: Contact Us</h4>
                            <p>Send a data deletion request to:</p>
                            <ul>
                                <li>Email: privacy@projectsaveng.org</li>
                                <li>Mail: P.O.Box 358, Ota-Ogun State, Nigeria</li>
                            </ul>

                            <p>We will process your request within 30 days and send confirmation once completed.</p>

                            <div class="note">
                                <strong>Note:</strong> Some information may be retained for legal or legitimate business purposes as required by law.
                            </div>
                        </div>
                    </div>
                    <section class="mb-5">
                        <h3>Contact Us</h3>
                        <p>For any questions about this Privacy Policy, please contact us at:</p>
                        <p>Email: info@projectsaveng.org</p>
                        <p>Phone: (+234) 07080100893</p>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <style>
        .privacy-content {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .privacy-content h3 {
            color: #FF4C4C;
            margin-bottom: 20px;
        }
        
        .privacy-content ul {
            padding-left: 20px;
        }
        
        .privacy-content ul li {
            margin-bottom: 10px;
            color: #6c757d;
        }
    </style>
</x-layouts.app>
