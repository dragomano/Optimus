import nextra from 'nextra';

const nextConfig = {
  output: 'export',
  basePath: '/Optimus',
  i18n: {
    locales: ['en', 'ru'],
    defaultLocale: 'en',
  },
};

const withNextra = nextra({
  theme: 'nextra-theme-docs',
  themeConfig: './theme.config.jsx',
});

export default withNextra(nextConfig);
