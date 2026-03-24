<!--
Footer de las páginas, nada en especial que explicar.  
-->
<script>
    import facebook from "$lib/assets/footer-icons/facebook-icon.svg";
    import instagram from "$lib/assets/footer-icons/instagram-icon.svg";
    import youtube from "$lib/assets/footer-icons/youtube-icon.svg";
    import tiktok from "$lib/assets/footer-icons/tiktok-icon.svg";
</script>

<footer>
    <div class="superior">
        <div class="full-logo">
            <p>IMAGEN CARA</p>
            <p>LOGO AQUI</p>
        </div>
        <div class="social-media">
            <p>Siguenos en nuestras redes sociales</p>
            <div class="social-media-icons">
                <img src={facebook} alt="logo facebook"/>
                <img src={instagram} alt="logo instagram"/>
                <img src={youtube} alt="logo youtube"/>
                <img src={tiktok} alt="logo tiktok"/>
            </div>
        </div>
    </div>
    <div class="inferior">
        <p>Copyright &copy; 2026, For Him. Todos los derechos reservados.</p>
    </div>
</footer>

<style lang="scss">
    footer {
        background-color: #2D3E40;
        padding: 16px 24px;
        margin-top: 100px;
        display: flex;
        flex-direction: column;
        gap: 32px;

        > .superior {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: space-between;
            gap: 36px;

            > .full-logo {
                display: flex;
            }

            > .social-media {
                display: flex;
                flex-direction: column;
                
                > p {
                    text-align: center;
                }
                
                > .social-media-icons {
                    display: flex;
                    flex-direction: row;
                    justify-content: center;
                    gap: 10px;
                }
            }
        }

        > .inferior {
            > p {
                text-align: center;
            }
        }
    }
</style>


