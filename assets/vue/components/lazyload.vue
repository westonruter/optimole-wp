<template>
    <div :class="{ 'saving--option' : this.$store.state.loading }">
        <div class="field columns">
            <label class="label column has-text-grey-dark">
                {{strings.enable_lazyload_placeholder_title}}
                <p class="is-italic has-text-weight-normal">
                    {{strings.enable_lazyload_placeholder_desc}}
                </p>
            </label>
            <div class="column is-3">
                <toggle-button :class="'has-text-dark'"
                               v-model="lazyloadPlaceholder"
                               :disabled="this.$store.state.loading"
                               :labels="{checked: strings.enabled, unchecked: strings.disabled}"
                               :width="80"
                               :height="25"
                               color="#008ec2"></toggle-button>
            </div>
        </div>
        <div class="field columns">
            <label class="label column has-text-grey-dark">
                {{strings.enable_bg_lazyload_title}}
                <p class="is-italic has-text-weight-normal">
                    {{strings.enable_bg_lazyload_desc}}
                </p>
            </label>
            <div class="column is-3">
                <toggle-button :class="'has-text-dark'"
                               v-model="lazyloadBgImages"
                               :disabled="this.$store.state.loading"
                               :labels="{checked: strings.enabled, unchecked: strings.disabled}"
                               :width="80"
                               :height="25"
                               color="#008ec2"></toggle-button>
            </div>
        </div>
        <div class="field columns" v-if="showBgSelectors">
            <div class="column">
                <label class="label has-text-grey-dark">
                    <span>{{strings.watch_title_lazyload}}</span>
                    <p class="is-italic has-text-weight-normal">
                        {{strings.watch_desc_lazyload}}
                    </p>
                </label>
                <div>
                    <textarea-autosize
                            class="textarea is-secondary is-small"
                            placeholder="e.g: .image, #item-id, div.with-background-image"
                            v-model="lazyloadSelectors"
                            :min-height="3"
                            :max-height="350"
                    ></textarea-autosize>
                </div>
            </div>
        </div>
        <div class="field  is-fullwidth columns ">
            <div class="column is-left">
                <button @click="saveChanges()" class="button is-success is-small "
                        :class="this.$store.state.loading ? 'is-loading'  : '' " :disabled="!showSave">
                    {{strings.save_changes}}
                </button>
            </div>
        </div>

    </div>
</template>

<script>
	export default {
		name: "lazyload",
		data() {
			return {
				strings: optimoleDashboardApp.strings.options_strings,
				all_strings: optimoleDashboardApp.strings,
				showSave: false,
                showBgSelectors: this.$store.state.site_settings.bg_replacer=== 'enabled',
				new_data: {},
			}
		},
		mounted: function () {
		},
		methods: {
			saveChanges: function () {
				this.$store.dispatch('saveSettings', {
					settings: this.new_data
				});
			},

		},
		computed: {
			site_settings() {
				return this.$store.state.site_settings;
			},
			lazyloadPlaceholder: {
				set: function (value) {
					this.showSave = true;
					this.new_data.lazyload_placeholder = value ? 'enabled' : 'disabled';
				},
				get: function () {
					return !(this.site_settings.lazyload_placeholder === 'disabled');
				}
			},
			lazyloadBgImages: {
				set: function (value) {
					this.showSave = true;
					this.new_data.bg_replacer = value ? 'enabled' : 'disabled';
					this.showBgSelectors = value;
				},
				get: function () {
					return !(this.site_settings.bg_replacer === 'disabled');
				}
			},
            lazyloadSelectors: {
	            set: function ( value ) {
                    this.showSave = true;
		            this.new_data.watchers = value;
	            },
	            get: function () {
		            return this.site_settings.watchers;
	            }
            }
		}
	}
</script>

<style scoped>

</style>