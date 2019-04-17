<style scoped>
html,body{
    height: 100%;
}

#myqrcode {
    margin:0 auto;
    height:100%;
    background:#404040;
    max-width:620px;
    position:relative;
}
    #myqrcode .myqrv{
        position: fixed;
        /* left: 50%; */
        /* top: 50%; */
        /* min-width: 300px; */
        /* min-height: 300px; */
        padding: 20px 0;
        /* top: 0; */
        margin: auto;
        background: #fff;
        /* bottom: 50%; */
        border-radius: 11px;
        text-align: center;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
#myqrcode .myqrv .headerpic{
    width:90%;
    height:60px;
    overflow:hidden;
    margin:0 auto;
    text-align: left;
    margin-bottom: 12px;

}
#myqrcode .myqrv .headerpic img{
display: inline-block;
}

#myqrcode .myqrv .headerpic span{
margin-left:10px;
}
#myqrcode .qr{
    width:90%;
    margin:0 auto;
}
#myqrcode .qr h1{
    color:#808080;
    margin-top:8px;
}
#myqrcode .qr img{
width:100%;
height:100%;
border-radius: 5px;
}
    #myqrcode .myqrv img{
border-radius: 5px;
display: block;
}
</style>
<template>
    <div id="myqrcode">
        <div class="myqrcodecontainer">
            <div class="myqrv">
                <img :src="userinfodata" width="300" height="400"/>
            </div>
        </div>
    </div>
</template>
<script>
var config = require('../../../config')
    export default {
        data(){
            return {
                 lists: [],
                 userinfo:{src:''},
                 userinfodata:{}
            }
        },
          components:{

            },
        mounted: function() {
                var obj3 = new Object();
              //  this.getuserfocus(obj3);
                this.getuserqrode();
            },
            methods:{
                    getuserfocus(obj){
                    var	that =this;
                    axios.get( decodeURIComponent(config.dev.env.default_domain_api)+'/user/qrcode', {
                        params: {
                            token: storeWithExpiration.get('token'),
                            userid: this.$route.query.userid
                        }
                    }) .then(function(response) {
                            if(response.status=='200'){
                                return response.data;
                            }
                        }).then(function(json) {
                            console.log(json)
                            if(typeof(json)!='undefined'){

                            }
                    }).catch(function(ex) {
                        console.log(ex);
                    });

                    },
                getuserqrode(){
                    var	that =this;
                    that.userinfodata=decodeURIComponent(config.dev.env.default_domain_api)+'/user/getUserImage?token='+storeWithExpiration.get('token')+'&userid='+this.$route.query.userid
                }

            },
            computed:{

            }
    }
</script>

