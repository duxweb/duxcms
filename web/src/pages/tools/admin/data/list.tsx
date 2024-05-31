import { useEffect, useMemo } from 'react'
import { useTranslate, useNavigation, useParsed, useCustom, useResource } from '@refinedev/core'
import { Button, EnhancedTableProps } from 'tdesign-react/esm'
import { PageTable, Modal, FormPage } from '@duxweb/dux-refine'
import { useMagicTableRender, MagicFormRender } from '@duxweb/dux-extend'

const RenderList = ({ data }: any) => {
  const { params } = useParsed()
  const translate = useTranslate()
  const { create } = useNavigation()
  const { resource } = useResource()

  const { columns, filters } = useMagicTableRender({
    fields: data?.data?.fields,
    magic: params?.name,
    editResource: data?.data?.page ? 'tools.data' : undefined,
    componentShow: () => import('./show'),
    componentEdit: () => import('./modal'),
    sourceUrl: '',
  })

  useEffect(() => {
    if (!resource?.meta) {
      return
    }
    resource.meta.label = data?.data?.label
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data])

  const table = useMemo<EnhancedTableProps>(() => {
    const config: EnhancedTableProps = {
      rowKey: 'id',
    }
    if (data?.data?.type == 'common') {
      config.pagination = undefined
    }
    if (data?.data?.type == 'tree') {
      config.tree = { childrenKey: 'children', treeNodeColumnIndex: 0, defaultExpandAll: true }
      config.pagination = undefined
    }
    return config
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data])

  return (
    <PageTable
      columns={columns}
      tableHook={{
        meta: {
          params: {
            magic: params?.name,
            action: 'show',
          },
        },
      }}
      table={table}
      actionRender={() =>
        data?.data?.page ? (
          <Button
            icon={<div className='t-icon i-tabler:plus'></div>}
            onClick={() => {
              create('tools.data')
            }}
          >
            {translate('buttons.create')}
          </Button>
        ) : (
          <Modal
            title={translate('buttons.create')}
            trigger={
              <Button icon={<div className='t-icon i-tabler:plus'></div>}>
                {translate('buttons.create')}
              </Button>
            }
            component={() => import('./modal')}
            componentProps={{
              magic: params?.name,
            }}
          ></Modal>
        )
      }
      filterRender={filters ? () => filters : undefined}
    />
  )
}

const RenderPage = () => {
  const { params } = useParsed()

  const { resource } = useResource()

  const { data } = useCustom<Record<string, any>>({
    url: 'tools/magic/config',
    method: 'get',
    meta: {
      params: {
        magic: params?.name,
      },
    },
    queryOptions: {
      cacheTime: 0,
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
      resource='tools.dataPage'
      back
      id={0}
      action='edit'
      useFormProps={{
        meta: {
          mode: 'page',
        },
        queryOptions: {
          cacheTime: 0,
        },
      }}
      queryParams={{
        magic: params?.name,
      }}
    >
      <div>{data?.data?.fields && <MagicFormRender fields={data?.data?.fields} />}</div>
    </FormPage>
  )
}

const ListData = () => {
  const { params } = useParsed()

  const { data }: any = useCustom<Record<string, any>>({
    url: 'tools/magic/config',
    method: 'get',
    meta: {
      params: {
        magic: params?.name,
      },
    },
  })

  if (data?.code !== 200) {
    return null
  }

  if (data.data.type === 'page') {
    return <RenderPage />
  }
  return <RenderList data={data} />
}

const List = () => {
  const { params } = useParsed()

  return <ListData key={params?.name} />
}

export default List
