export const Nav = {
    data: function () {
        return {
            navIsOpen: false,
        }
    },
    watch: {
        navIsOpen: function (val) {
            console.log(val);
        },
    },
    methods: {
        openNav() {
            this.navIsOpen = true;
            document.body.classList.add('navigationIsOpen');
        },
        closeNav() {
            document.body.classList.remove('navigationIsOpen');
            this.navIsOpen = false;
        }
    },
    created() {
        console.log('Nav');
    }
}

export const Messages = {
    methods: {
        closeMessages() {
            // Find error message container.
            const messages = document.querySelector(".m-messages");
            // Remove it
            messages.remove();
        },

    },
    created() {
        console.log('Messages');
    }
}