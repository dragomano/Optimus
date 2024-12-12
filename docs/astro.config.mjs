import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';
import starlightLinksValidator from 'starlight-links-validator'

// https://astro.build/config
export default defineConfig({
	site: 'https://dragomano.github.io/Optimus',
	base: '/Optimus/',
	integrations: [
		starlight({
			plugins: [starlightLinksValidator({errorOnRelativeLinks: false})],
			customCss: [
				'./src/styles/custom.css',
			],
			title: 'Optimus Docs',
			description: 'Guide to Setting Up and Using Optimus',
			defaultLocale: 'root',
			locales: {
				root: {
					label: 'English',
					lang: 'en',
				},
				ru: {
					label: 'Русский',
					lanag: 'ru',
				}
			},
			social: {
				github: 'https://github.com/dragomano/Optimus',
			},
			sidebar: [
				{
					label: 'Greetings',
					translations: {
						'ru': 'Приветствие',
					},
					link: '/',
				},
				{
					label: 'Settings',
					translations: {
						'ru': 'Настройки',
					},
					autogenerate: { directory: 'settings' },
				},
				{
					label: 'Addons',
					translations: {
						'ru': 'Аддоны',
					},
					autogenerate: { directory: 'addons' },
				},
			],
		}),
	],
});
