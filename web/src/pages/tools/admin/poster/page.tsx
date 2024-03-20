import { useTranslate, useResource } from '@refinedev/core'
import { FormPage, FormPageItem } from '@duxweb/dux-refine'
import { Input } from 'tdesign-react/esm'
import { Poster } from '@duxweb/dux-extend'

const Page = () => {
  const translate = useTranslate()
  const { id } = useResource()

  return (
    <FormPage
      formProps={{
        labelAlign: 'top',
      }}
      back
      useFormProps={{
        queryOptions: {
          cacheTime: 0,
        },
      }}
      id={id}
    >
      <FormPageItem name='title'>
        <Input placeholder={translate('tools.poster.validate.title')} />
      </FormPageItem>

      <FormPageItem name='data'>
        <Poster />
      </FormPageItem>
    </FormPage>
  )
}

export default Page
