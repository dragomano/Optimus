import { useRouter } from 'next/router'
import { useConfig } from 'nextra-theme-docs'
//import { useRouter } from 'nextra/hooks'

export default {
  head() {
    const { asPath, defaultLocale, locale, route } = useRouter()
    const config = useConfig()
    const url =
      'https://dragomano.github.io/Optimus' +
      (defaultLocale === locale ? asPath : `/${locale}${asPath}`)
    const description =
      config.frontMatter.description ||
      'Guide to Setting Up and Using Optimus'
    const title = config.title + (route === '/' ? '' : ' - Optimus Docs')

    return (
      <>
        <meta property="og:url" content={url} />
        <meta property="og:title" content={title} />
        <meta property="og:description" content={description}/>
        <title>{title}</title>
      </>
    )
  },
  logo: <span>Optimus Docs</span>,
  project: {
    link: 'https://github.com/dragomano/Optimus'
  },
  editLink: {
    component: null
  },
  feedback: {
    content: null
  },
  footer: {
    content: (
      <>&copy; 2024, Optimus Docs</>
    )
  },
  i18n: [
    { locale: 'en', name: 'English' },
    { locale: 'ru', name: 'Русский' },
  ]
}