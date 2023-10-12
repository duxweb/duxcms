import React, { useEffect, useRef, useState } from 'react'
import { useTranslate, useDelete } from '@refinedev/core'
import { PrimaryTableCol, Button, Link, Popconfirm, Select, Form } from 'tdesign-react/esm'
import { PageTable, Modal, useSelect, TableRef } from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()
  const { mutate } = useDelete()
  const table = useRef<TableRef>(null)
  const [init, setInit] = useState(false)
  const { options, onSearch, queryResult } = useSelect({
    resource: 'content.menu',
    optionLabel: 'title',
    optionValue: 'id',
  })

  useEffect(() => {
    if (!options.length && !init) {
      return
    }
    setInit(() => true)
    table.current?.form?.setFieldsValue({
      menu_id: options[0]?.value,
    })
  }, [init, options])

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      {
        colKey: 'id',
        sorter: true,
        sortType: 'all',
        title: 'ID',
        width: 150,
      },
      {
        colKey: 'title',
        title: translate('content.menu.fields.title'),
        ellipsis: true,
      },
      {
        colKey: 'url',
        title: translate('content.menu.fields.url'),
        ellipsis: true,
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 160,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <Modal
                title={translate('buttons.edit')}
                trigger={<Link theme='primary'>{translate('buttons.edit')}</Link>}
                component={() => import('./save')}
                componentProps={{ id: row.id, menu_id: row.menu_id }}
              />
              <Popconfirm
                content={translate('buttons.confirm')}
                destroyOnClose
                placement='top'
                showArrow
                theme='default'
                onConfirm={() => {
                  mutate({
                    resource: 'content.menuData',
                    id: row.id,
                    meta: {
                      params: {
                        menu_id: table.current?.filters.menu_id,
                      },
                    },
                  })
                }}
              >
                <Link theme='danger'>{translate('buttons.delete')}</Link>
              </Popconfirm>
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate]
  )

  return (
    <>
      <PageTable
        ref={table}
        columns={columns}
        table={{
          rowKey: 'id',
          tree: { childrenKey: 'children', treeNodeColumnIndex: 1, defaultExpandAll: true },
          pagination: undefined,
        }}
        actionRender={() => (
          <>
            {table.current?.filters?.menu_id && (
              <Modal
                title={translate('buttons.create')}
                trigger={
                  <Button icon={<div className='i-tabler:plus mr-2' />}>
                    {translate('buttons.create')}
                  </Button>
                }
                component={() => import('./save')}
                componentProps={{
                  menu_id: table.current?.filters?.menu_id,
                }}
              ></Modal>
            )}
          </>
        )}
        filterRender={() => (
          <>
            <div>
              <Modal
                title={translate('content.menu.fields.addGroup')}
                trigger={
                  <Button icon={<div className='i-tabler:plus' />} variant='outline'></Button>
                }
                component={() => import('./group')}
              ></Modal>
            </div>
            <Form.FormItem name='menu_id' initialData={options?.[0]?.value}>
              <Select
                filterable
                clearable
                onSearch={onSearch}
                options={options}
                placeholder={translate('content.menu.placeholder.group')}
                loading={queryResult.isLoading}
              />
            </Form.FormItem>

            {table.current?.filters?.menu_id && (
              <div className='flex gap-2'>
                <Modal
                  title={translate('content.menu.fields.editGroup')}
                  trigger={
                    <Button icon={<div className='i-tabler:edit' />} variant='outline'></Button>
                  }
                  component={() => import('./group')}
                  componentProps={{
                    id: table.current?.filters.menu_id,
                  }}
                ></Modal>
                <Popconfirm
                  content={translate('buttons.confirm')}
                  destroyOnClose
                  placement='top'
                  showArrow
                  theme='default'
                  onConfirm={() => {
                    mutate({
                      resource: 'content.menu',
                      id: table.current?.filters.menu_id,
                    })
                    setInit(false)
                  }}
                >
                  <Button icon={<div className='i-tabler:trash' />} variant='outline'></Button>
                </Popconfirm>
              </div>
            )}
          </>
        )}
      />
    </>
  )
}

export default List
