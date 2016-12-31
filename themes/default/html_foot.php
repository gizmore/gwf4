		</div>

	</section>
	
	<section layout="row" flex class="gwf-dynamic-content gwf-bottom-content">{{data.bottomContent}}</section>

	<gwf-loading-pane ng-hide="!showLoadingBackdrop()">
		<md-progress-circular ng-disabled="!showLoadingBackdrop()" class="md-hue-2" ></md-progress-circular>
	</gwf-loading-pane>

	<script>angular.element(document).ready(function() { angular.bootstrap(document.body, ['gwf4']); });</script>

</body>
