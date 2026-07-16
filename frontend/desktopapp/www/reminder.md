# UI Implementation Reminders

Please adhere to the following rules when building or modifying UI elements across the platform:

1. **Password Input Fields:**
   * All password input fields must feature interactive show/hide toggle controls (e.g., an eye icon) for user convenience and feedback.

2. **System Alert Actions:**
   * Do not use standard native browser alerts or basic alert blocks.
   * All user alerts, success messages, verification confirmations, or action errors must be implemented in the form of **SweetAlert** modals.

3. **Button Interactivity & Click Prevention:**
   * Do not allow users to double-click buttons.
   * All buttons must disable click events instantly upon the initial trigger.
   * During asynchronous requests, the button text/icons must transform into a circular progress loading spinner, remaining disabled until the backend action completes and returns a response.
