import React from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol, Form } from 'tdesign-react/esm'
import {
  PageTable,
  EditLinkModal,
  DeleteLink,
  CreateButtonModal,
  FilterEdit,
} from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()
  const [form] = Form.useForm()

  const menuId = Form.useWatch('menu_id', form)

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
              <EditLinkModal
                rowId={row.id}
                component={() => import('./save')}
                componentProps={{ menu_id: row.menu_id }}
              />
              <DeleteLink
                rowId={row.id}
                params={{
                  menu_id: row.menu_id,
                }}
              />
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )

  return (
    <>
      <PageTable
        columns={columns}
        filterForm={form}
        table={{
          rowKey: 'id',
          tree: { childrenKey: 'children', treeNodeColumnIndex: 1, defaultExpandAll: true },
          pagination: undefined,
        }}
        actionRender={() => (
          <>
            {menuId && (
              <CreateButtonModal
                title={translate('buttons.create')}
                component={() => import('./save')}
                componentProps={{
                  menu_id: menuId,
                }}
              ></CreateButtonModal>
            )}
          </>
        )}
        filterRender={() => (
          <>
            <FilterEdit
              title={translate('content.menu.placeholder.group')}
              resource='content.menu'
              form={form}
              field='menu_id'
              defaultSelect
              optionLabel='name'
              optionValue='id'
              component={() => import('./group')}
            />
          </>
        )}
      />
    </>
  )
}

export default List
