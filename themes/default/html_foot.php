		</div>

		<ui-view class="gwf-angular-main-view" layout="row" flex></ui-view>

	</section>
	
	<section layout="row" flex class="gwf-dynamic-content gwf-bottom-content">{{data.bottomContent}}</section>

	<script>angular.element(document).ready(function() { angular.bootstrap(document.body, ['gwf4']); });</script>

</body>
