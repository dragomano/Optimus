import nextra from 'nextra';

const nextConfig = {
  output: 'export',
  //basePath: '/Optimus',
};

const withNextra = nextra({
  theme: 'nextra-theme-docs',
  themeConfig: './theme.config.jsx',
  i18n: {
    locales: ['en', 'ru'],
    defaultLocale: 'en',
  },
});

export default withNextra(nextConfig)
