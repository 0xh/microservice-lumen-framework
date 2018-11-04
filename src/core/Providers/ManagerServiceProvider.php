<?php

	namespace Core\Providers;

	use Illuminate\Support\ServiceProvider;

	class ManagerServiceProvider extends ServiceProvider
	{
		private static $coreProviders;

		public function boot(){
			$this->bootConfig();
		}

		/**
		 * Register any application services.
		 *
		 * @return void
		 */
		public function register()
		{
			$this->registerProviders();
			$this->registerAlias();
		}

		protected function registerProviders()
		{
			/**
			 * Load Global Providers
			 */
			$globalProviders = config('providers.global') ?: $this->coreProviders();

			foreach ($globalProviders as $globalProvider) {
				$this->app->register($globalProvider);
			}

			/**
			 * Load Local Providers if env is 'local'
			 */
			if (app()->environment('local')) {
				$localProviders = config('providers.local') ?: [];
				foreach ($localProviders as $localProvider) {
					$this->app->register($localProvider);
				}
			}

			/**
			 * Load Production Providers if env is 'production'
			 */
			if (app()->environment('production')) {
				$productionProviders = config('providers.production');
				foreach ($productionProviders as $productionProvider) {
					$this->app->register($productionProvider);
				}
			}
		}

		/**
		 * Load alias
		 */
		protected function registerAlias()
		{
			$aliases = config('providers.alias') ?: [];
			foreach ($aliases as $key => $value) {
				class_alias($value, $key);
			}
		}

		/**
		 * Load config
		 */
		protected function bootConfig() {
			$this->app->configure('providers');
		}

		/**
		 * Load core providers if config file not exist
		 *
		 * @return array
		 */
		private function coreProviders()
		{
			self::$coreProviders = [
				'core' => \Core\Providers\CoreServiceProvider::class,
				'auth' => \Core\Providers\AuthServiceProvider::class,
				'graphql' => \Core\Providers\GraphQLServiceProvider::class,
			];

			return self::$coreProviders;
		}
	}
