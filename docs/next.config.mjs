import nextra from 'nextra';

const withNextra = nextra({
  output: 'export',
  //basePath: '/Optimus',
  theme: 'nextra-theme-docs',
  themeConfig: './theme.config.jsx',
});

export default withNextra({
  i18n: {
    locales: ['en', 'ru'],
    defaultLocale: 'en',
  },
});
