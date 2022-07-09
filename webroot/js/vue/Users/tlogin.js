var app = new Vue({
    el: '#app',
    components: {vueTelegramLogin},
    directives: {
      infiniteScroll : window.infiniteScroll,
    },
    async mounted() {
      this.loading = false;
    },
    data: function () {
      return {
        
      };
    },
    computed: {
      
      
    },
    methods: {
        async telegramLogin(user){
            
            const data = new FormData()
            data.append('user', JSON.stringify(user))
            let response = await axios.post('/login.json', data)
            console.log("response", response)
            if(response.data.success) {                
                window.location.href = response.data.redirectURL
            }
        }
    }
  });
  