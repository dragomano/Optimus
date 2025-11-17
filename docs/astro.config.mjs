import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';
import starlightLinksValidator from 'starlight-links-validator';
import { remarkHeadingId } from "remark-custom-heading-id";
import starlightUiTweaks from 'starlight-ui-tweaks';

// https://astro.build/config
export default defineConfig({
	site: 'https://dragomano.github.io/Optimus',
	base: '/Optimus/',
	integrations: [
		starlight({
			plugins: [
				starlightUiTweaks(),
				starlightLinksValidator({
					errorOnRelativeLinks: false
				})
			],
			customCss: [
				'./src/styles/custom.scss',
			],
			title: '🤖 Optimus Docs',
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
			social: [
				{ icon: 'github', label: 'GitHub', href: 'https://github.com/dragomano/Optimus' },
			],
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
	markdown: {
		remarkPlugins: [remarkHeadingId],
	},
});
