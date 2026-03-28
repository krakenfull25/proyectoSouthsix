<!--
Footer de las páginas, nada en especial que explicar.  
-->
<script>
    import facebook from "$lib/assets/footer-icons/facebook-icon.svg";
    import instagram from "$lib/assets/footer-icons/instagram-icon.svg";
    import youtube from "$lib/assets/footer-icons/youtube-icon.svg";
    import tiktok from "$lib/assets/footer-icons/tiktok-icon.svg";
    import silhouette from "$lib/assets/footer-icons/Logo.png";
</script>

<footer>
    <div class="superior">
        <div class="full-logo">
            <img src={silhouette} alt="silueta logo"/>
            
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
		flex-direction: column; /* mobile-first: columna en pantalla pequeña */
		flex-wrap: wrap;
		justify-content: center;
		align-items: center;
		gap: 24px;

		> .full-logo {
			display: flex;

			img {
				width: 150px;
				height: auto;
				object-fit: contain;
			}
		}

		> .social-media {
			display: flex;
			flex-direction: column;
			align-items: center;

			> p {
				text-align: center;
				color: white;
				margin-bottom: 8px;
			}

			> .social-media-icons {
				display: flex;
				flex-direction: row;
				justify-content: center;
				gap: 10px;

				img {
					width: 30px;
					height: auto;
				}
			}
		}
	}

	> .inferior {
		> p {
			text-align: center;
			color: white;
			font-size: 14px;
		}
	}

	@media (min-width: 1024px) {
		> .superior {
			flex-direction: row;
			align-items: center;
			justify-content: space-between; 
			> .full-logo img {
				width: 150px;
			}

			> .social-media {
				align-items: flex-end;
			}

			> .social-media > .social-media-icons img {
				width: 35px;
			}
		}
	}
}
</style>


