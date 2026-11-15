<?php
/**
 * Contact form (used by the [rk_contact_form] shortcode on the Contact page).
 * Submissions go to inc/contact.php: e-mailed to the site address and saved under Dashboard → Enquiries.
 *
 * @package RK_Developers
 */

?>
<form class="contact-form" id="contactForm" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" novalidate>
  <input type="hidden" name="action" value="rk_contact" />
  <input type="hidden" name="source" value="Contact page" />
  <div style="position:absolute;left:-9999px;" aria-hidden="true"><label>Website <input type="text" name="website" id="rkWebsite" tabindex="-1" autocomplete="off" /></label></div>
  <div class="form-row">
    <div class="form-group">
      <label for="name">Full Name *</label>
      <div class="input-wrap">
        <i class="fas fa-user icon-left"></i>
        <input type="text" id="name" name="name" placeholder="Your full name" required />
      </div>
      <span class="field-error" id="nameError"></span>
    </div>
    <div class="form-group">
      <label for="phone">Phone Number *</label>
      <div class="input-wrap">
        <i class="fas fa-phone icon-left"></i>
        <input type="tel" id="phone" name="phone" placeholder="10-digit mobile number" required />
      </div>
      <span class="field-error" id="phoneError"></span>
    </div>
  </div>

  <div class="form-group">
    <label for="email">Email Address</label>
    <div class="input-wrap">
      <i class="fas fa-envelope icon-left"></i>
      <input type="email" id="email" name="email" placeholder="your@email.com" />
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="interest">I'm Interested In *</label>
      <div class="input-wrap">
        <i class="fas fa-home icon-left"></i>
        <select id="interest" name="interest" required>
          <option value="">Select property type</option>
          <option>Residential Plots</option>
          <option>Farm Plots</option>
          <option>Commercial Plots</option>
          <option>General Inquiry</option>
        </select>
        <i class="fas fa-chevron-down select-arrow"></i>
      </div>
      <span class="field-error" id="interestError"></span>
    </div>
    <div class="form-group">
      <label for="budget">Budget Range</label>
      <div class="input-wrap">
        <i class="fas fa-rupee-sign icon-left"></i>
        <select id="budget" name="budget">
          <option value="">Select budget</option>
          <option>Under &#8377;50 Lakhs</option>
          <option>&#8377;50L &#8211; &#8377;1 Crore</option>
          <option>&#8377;1 Crore &#8211; &#8377;2 Crore</option>
          <option>&#8377;2 Crore &#8211; &#8377;5 Crore</option>
          <option>Above &#8377;5 Crore</option>
        </select>
        <i class="fas fa-chevron-down select-arrow"></i>
      </div>
    </div>
  </div>

  <div class="form-group">
    <label for="message">Message *</label>
    <div class="input-wrap" style="align-items:flex-start;">
      <i class="fas fa-comment-alt icon-left" style="top:13px;position:absolute;"></i>
      <textarea id="message" name="message" rows="4" placeholder="Tell us about your requirements&#8230;" required></textarea>
    </div>
    <span class="field-error" id="messageError"></span>
  </div>

  <div class="form-checkbox">
    <input type="checkbox" id="consent" name="consent" required />
    <label for="consent">I agree to be contacted by RK Developers regarding my inquiry. I understand my information will be handled as per the <a href="#privacy-policy">Privacy Policy</a>.</label>
  </div>
  <span class="field-error" id="consentError"></span>

  <button type="submit" class="btn-submit" id="submitBtn">
    <span class="btn-text">Send Inquiry <i class="fas fa-paper-plane"></i></span>
    <span class="btn-loading" style="display:none;"><i class="fas fa-spinner fa-spin"></i> Sending&#8230;</span>
  </button>
  <p class="field-error" id="formError" style="display:none;text-align:center;margin-top:10px;"></p>
</form>

<div class="form-success" id="formSuccess" style="display:none;">
  <div class="success-icon"><i class="fas fa-check-circle"></i></div>
  <h3>Thank You!</h3>
  <p>Your inquiry has been received. Our team will contact you within 24 hours.</p>
  <button onclick="resetForm()" style="background:linear-gradient(135deg,#F97316,#DC2626);color:#fff;border:none;padding:12px 28px;border-radius:50px;font-family:'Inter',sans-serif;font-weight:600;cursor:pointer;font-size:0.9rem;">Send Another Inquiry</button>
</div>
