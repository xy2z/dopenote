window.axios = require('axios');

// Wrapper for axios, so it's easier to replace if needed.
class Ajax {

    static post(obj) {
        // Callback before response
        if (Ajax.before_post) {
            Ajax.before_post()
        }

        if (!obj.data) {
            obj.data = {}
        }

        // Send post request.
        axios
        .post(obj.url, obj.data)
        .then(response => {
            // Callback after response
            if (Ajax.after_post) {
                Ajax.after_post()
            }

            // Callback on success
            if (obj.success) {
                obj.success(response)
            }
        })
    }
}

export default Ajax