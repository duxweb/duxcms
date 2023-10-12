import { useResource, useCustom, useParsed } from '@refinedev/core'
import { FormPage } from '@duxweb/dux-refine'
import { MagicFormRender } from '@duxweb/dux-extend'

const Page = () => {
  const { id, params } = useParsed()
  const { resource } = useResource()
  const magic = params?.name

  const { data } = useCustom<Record<string, any>>({
    url: 'tools/magic/config',
    method: 'get',
    meta: {
      params: {
        magic: magic,
      },
    },
  })

  if (resource?.meta) {
    resource.meta.label = data?.data?.label
  }

  return (
    <FormPage
      formProps={{
        labelAlign: 'top',
      }}
      back
      id={id}
      queryParams={{
        magic: magic,
      }}
    >
      {data?.data?.fields && <MagicFormRender fields={data?.data?.fields} />}
    </FormPage>
  )
}

export default Page
