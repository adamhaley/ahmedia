{{-- Static copy ported from Codette's contactForm pattern -- not yet wired to a
     real submission handler (dreamworldcirque's StoreContactSubmissionController
     is the reference for building that out; out of scope for this scaffold). --}}
<section class="section" id="contact">
  <div class="container">
    <div class="section-heading">
      <div>
        <p class="eyebrow">Contact</p>
        <h2 class="section-title">Let's talk about what you're trying to build</h2>
      </div>
      <p class="section-copy">Send a few details and we'll get back to you — or use the chat in the corner for something quicker.</p>
    </div>
    <form class="contact-form surface-card" action="#" method="post">
      <div class="contact-form-row">
        <label for="contact-form-name">Name</label>
        <input id="contact-form-name" type="text" name="name" placeholder="Your name" required />
      </div>
      <div class="contact-form-row">
        <label for="contact-form-email">Email</label>
        <input id="contact-form-email" type="email" name="email" placeholder="you@example.com" required />
      </div>
      <div class="contact-form-row">
        <label for="contact-form-message">Message</label>
        <textarea id="contact-form-message" name="message" rows="5" placeholder="What are you trying to build?" required></textarea>
      </div>
      <button class="button button-primary" type="submit">Send Message</button>
    </form>
  </div>
</section>
