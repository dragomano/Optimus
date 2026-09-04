import { defineConfig } from 'astro/config';
import { satteri } from '@astrojs/markdown-satteri';
import starlight from '@astrojs/starlight';
import starlightLinksValidator from 'starlight-links-validator';
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
					lang: 'ru',
				}
			},
			social: [
				{ icon: 'github', label: 'GitHub', href: 'https://github.com/dragomano/Optimus' },
			],
			sidebar: [
				{
					label: 'Greetings',
					translations: { 'ru': 'Приветствие' },
					link: '/',
				},
				{
					label: 'Settings',
					translations: { 'ru': 'Настройки' },
					items: [{ autogenerate: { directory: 'settings' } }],
				},
				{
					label: 'Addons',
					translations: { 'ru': 'Аддоны' },
					items: [{ autogenerate: { directory: 'addons' } }],
				},
			],
		}),
	],
	vite: {
		css: {
			preprocessorOptions: {
				scss: {
					quietDeps: true,
				},
			},
		},
	},
	markdown: {
		processor: satteri({
			features: { headingAttributes: true },
		}),
	},
});
